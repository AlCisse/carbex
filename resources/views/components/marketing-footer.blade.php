{{-- Marketing Footer Component --}}
<footer class="py-16 border-t" style="border-color: var(--border); background-color: var(--bg-card);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-6 gap-8 mb-12">
            {{-- Logo & Description --}}
            <div class="md:col-span-2">
                <a href="/" class="block mb-4">
                    <x-logo />
                </a>
                <p class="text-sm mb-4" style="color: var(--text-muted);">
                    {{ __('carbex.footer.description') }}
                </p>
                <div class="flex items-center gap-3">
                    <a href="https://linkedin.com/company/carbex" target="_blank" rel="noopener" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="LinkedIn">
                        <svg class="w-5 h-5" style="color: var(--text-muted);" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com/carbex_de" target="_blank" rel="noopener" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Twitter">
                        <svg class="w-5 h-5" style="color: var(--text-muted);" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Information --}}
            <div>
                <p class="text-sm font-semibold mb-4" style="color: var(--text-primary);">{{ __('carbex.footer.information') }}</p>
                <ul class="space-y-2.5 text-sm" style="color: var(--text-secondary);">
                    <li><a href="{{ route('cgv') }}" class="hover:underline">{{ __('carbex.footer.terms_sale') }}</a></li>
                    <li><a href="{{ route('cgu') }}" class="hover:underline">{{ __('carbex.footer.terms_use') }}</a></li>
                    <li><a href="{{ route('engagements') }}" class="hover:underline">{{ __('carbex.footer.commitments') }}</a></li>
                    <li><a href="{{ route('mentions-legales') }}" class="hover:underline">{{ __('carbex.footer.legal_notice') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:underline">{{ __('carbex.footer.contact') }}</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div>
                <p class="text-sm font-semibold mb-4" style="color: var(--text-primary);">{{ __('carbex.footer.resources') }}</p>
                <ul class="space-y-2.5 text-sm" style="color: var(--text-secondary);">
                    <li><a href="{{ route('blog.index') }}" class="hover:underline">{{ __('carbex.footer.blog') }}</a></li>
                    <li><a href="{{ route('blog.index') }}?tag=guides" class="hover:underline">{{ __('carbex.footer.guides') }}</a></li>
                    <li><a href="{{ route('blog.index') }}?tag=bilan-carbone" class="hover:underline">{{ __('carbex.footer.carbon_footprint') }}</a></li>
                    <li><a href="{{ route('blog.index') }}?tag=reglementation" class="hover:underline">{{ __('carbex.footer.csrd_regulation') }}</a></li>
                </ul>
            </div>

            {{-- Discover --}}
            <div>
                <p class="text-sm font-semibold mb-4" style="color: var(--text-primary);">{{ __('carbex.footer.discover') }}</p>
                <ul class="space-y-2.5 text-sm" style="color: var(--text-secondary);">
                    <li><a href="/#features" class="hover:underline">{{ __('carbex.footer.features') }}</a></li>
                    <li><a href="{{ route('pour-qui') }}" class="hover:underline">{{ __('carbex.footer.for_whom') }}</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:underline">{{ __('carbex.footer.pricing') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:underline">{{ __('carbex.footer.free_trial') }}</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <p class="text-sm font-semibold mb-4" style="color: var(--text-primary);">{{ __('carbex.footer.company') }}</p>
                <ul class="space-y-2.5 text-sm" style="color: var(--text-secondary);">
                    <li><a href="/partenariat" class="hover:underline">{{ __('carbex.footer.partnership') }}</a></li>
                    <li><a href="/carrieres" class="hover:underline">{{ __('carbex.footer.careers') }}</a></li>
                    <li><a href="/presse" class="hover:underline">{{ __('carbex.footer.press') }}</a></li>
                </ul>
            </div>
        </div>

        {{-- Certifications & Standards --}}
        <div class="py-6 border-t border-b mb-8" style="border-color: var(--border);">
            <div class="flex flex-wrap items-center justify-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--accent-light);">
                        <span class="text-xs font-bold" style="color: var(--accent);">ADEME</span>
                    </div>
                    <span class="text-xs" style="color: var(--text-muted);">Base Carbone</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--accent-light);">
                        <span class="text-xs font-bold" style="color: var(--accent);">GHG</span>
                    </div>
                    <span class="text-xs" style="color: var(--text-muted);">Protocol</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--accent-light);">
                        <span class="text-xs font-bold" style="color: var(--accent);">ISO</span>
                    </div>
                    <span class="text-xs" style="color: var(--text-muted);">14064-1</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--accent-light);">
                        <span class="text-xs font-bold" style="color: var(--accent);">{{ __('carbex.footer.gdpr') }}</span>
                    </div>
                    <span class="text-xs" style="color: var(--text-muted);">{{ __('carbex.footer.compliant') }}</span>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm" style="color: var(--text-muted);">
                &copy; {{ date('Y') }} Carbex GmbH. {{ __('carbex.footer.all_rights_reserved') }}
            </p>
            <div class="flex items-center gap-6 text-sm" style="color: var(--text-muted);">
                <a href="{{ route('mentions-legales') }}" class="hover:underline">{{ __('carbex.footer.privacy') }}</a>
                <a href="{{ route('cgu') }}" class="hover:underline">{{ __('carbex.footer.terms') }}</a>
                <a href="{{ route('mentions-legales') }}#cookies" class="hover:underline">{{ __('carbex.footer.cookies') }}</a>
            </div>
        </div>
    </div>
</footer>
