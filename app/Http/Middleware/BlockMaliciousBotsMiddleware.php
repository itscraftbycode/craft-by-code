<?php

namespace App\Http\Middleware;

use App\Security\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMaliciousBotsMiddleware
{
    /**
     * Known malicious automated vulnerability scanners, scraper bots, and attack tools.
     */
    private const MALICIOUS_USER_AGENTS = [
        'sqlmap',
        'nikto',
        'acunetix',
        'havij',
        'dirbuster',
        'wpscan',
        'nuclei',
        'gobuster',
        'masscan',
        'zgrab',
        'shodan',
        'censys',
        'nmap',
        'openvas',
        'nessus',
        'metasploit',
        'burpsuite',
        'arachni',
        'netsparker',
        'jaeles',
        'ffuf',
        'wfuzz',
    ];

    /**
     * Dangerous exploit signatures in request URI and query parameters.
     */
    private const EXPLOIT_PATTERNS = [
        '/(\.\.[\/\\\\])/i',                             // Path traversal (../ or ..\)
        '/(\/etc\/passwd|\/etc\/shadow|win\.ini)/i',     // Sensitive system files
        '/(union\s+select|select\s+.*\s+from\s+)/i',      // SQL injection
        '/(base64_decode|eval\(|passthru\(|shell_exec\()/i', // Remote code execution
        '/(<script|%3Cscript|<iframe|%3Ciframe)/i',      // Cross-Site Scripting (XSS)
        '/(benchmark\s*\(|sleep\s*\(\d+\))/i',           // Time-based blind SQLi
        '/(\bxp_cmdshell\b|\bexec\s+xp_)/i',             // Database cmd execution
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower((string) $request->userAgent());

        // 1. Check for malicious scanner user-agents
        foreach (self::MALICIOUS_USER_AGENTS as $bot) {
            if ($userAgent !== '' && str_contains($userAgent, $bot)) {
                SecurityLogger::log('malicious_bot_blocked', "Blocked known security scanner: {$bot}", 'high', null, $request, [
                    'matched_bot' => $bot,
                    'user_agent' => substr((string) $request->userAgent(), 0, 200),
                ]);

                return response()->json([
                    'status' => 'error',
                    'code' => 'MALICIOUS_BOT_BLOCKED',
                    'message' => 'Access denied by automated threat defense.',
                ], 403);
            }
        }

        // 2. Check for empty user-agent on mutation endpoints (POST/PUT/PATCH/DELETE)
        if ($userAgent === '' && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            SecurityLogger::log('malicious_bot_blocked', 'Blocked mutation request with empty User-Agent', 'warning', null, $request);

            return response()->json([
                'status' => 'error',
                'code' => 'USER_AGENT_REQUIRED',
                'message' => 'A valid User-Agent header is required for this request.',
            ], 400);
        }

        // 3. Inspect request URI and query strings for exploit signatures
        $rawUri = urldecode($request->getRequestUri());
        foreach (self::EXPLOIT_PATTERNS as $pattern) {
            if (preg_match($pattern, $rawUri)) {
                SecurityLogger::log('malicious_exploit_blocked', 'Blocked request containing attack payload signature', 'critical', null, $request, [
                    'matched_pattern' => $pattern,
                    'uri' => substr($rawUri, 0, 250),
                ]);

                return response()->json([
                    'status' => 'error',
                    'code' => 'EXPLOIT_PAYLOAD_DETECTED',
                    'message' => 'Request rejected due to potentially malicious characters.',
                ], 400);
            }
        }

        return $next($request);
    }
}

