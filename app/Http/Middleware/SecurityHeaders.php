<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to protect against common web vulnerabilities.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add headers to HTML responses
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }

        // Strict-Transport-Security (HSTS)
        // Force HTTPS for 1 year, include subdomains
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // X-Frame-Options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options - Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Legacy XSS protection for older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy - Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Restrict browser features
        $response->headers->set(
            'Permissions-Policy',
            'accelerometer=(), camera=(), geolocation=(self), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        );

        // Cross-Origin-Opener-Policy (COOP) - Isolate browsing context
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        // Cross-Origin-Embedder-Policy (COEP)
        // Using unsafe-none to allow third-party resources (fonts, CDN scripts)
        $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');

        // Content-Security-Policy
        $csp = $this->buildContentSecurityPolicy();
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Build the Content-Security-Policy header value.
     */
    protected function buildContentSecurityPolicy(): string
    {
        $policies = [
            // Default fallback
            "default-src 'self'",

            // Scripts: self, inline (for Alpine.js), CDNs
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://www.googletagmanager.com https://www.google-analytics.com https://www.paypal.com https://www.sandbox.paypal.com",

            // Styles: self, inline (for Tailwind), Google Fonts, CDNs
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",

            // Images: self, data URIs, storage, external
            "img-src 'self' data: blob: https: http:",

            // Fonts: self, Google Fonts
            "font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com data:",

            // Connections: self, analytics, payment providers
            "connect-src 'self' https://www.google-analytics.com https://www.paypal.com https://www.sandbox.paypal.com wss:",

            // Frames: payment providers
            "frame-src 'self' https://www.paypal.com https://www.sandbox.paypal.com https://www.google.com",

            // Frame ancestors: prevent embedding
            "frame-ancestors 'self'",

            // Form submissions
            "form-action 'self' https://www.paypal.com https://www.sandbox.paypal.com",

            // Base URI restriction
            "base-uri 'self'",

            // Object sources (Flash, etc.)
            "object-src 'none'",

            // Upgrade insecure requests in production
            "upgrade-insecure-requests",
        ];

        return implode('; ', $policies);
    }

    /**
     * Check if the response is an HTML response.
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html') ||
               empty($contentType) ||
               $response->getStatusCode() === 200 && !$response->headers->has('Content-Type');
    }
}
