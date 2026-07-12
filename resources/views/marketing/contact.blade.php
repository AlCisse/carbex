@extends('layouts.marketing')

@section('title', __('linscarbon.marketing.contact.title') . ' - LinsCarbon')
@section('description', __('linscarbon.marketing.contact.description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.marketing.contact.title') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.marketing.contact.hero_title') }}
            </h1>
            <p class="text-lg" style="color: var(--text-secondary);">
                {{ __('linscarbon.marketing.contact.hero_subtitle') }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div>
                <h2 class="text-xl font-semibold mb-6" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.contact_us') }}</h2>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: var(--accent-light);">
                            <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.email') }}</p>
                            <a href="mailto:contact@linscarbon.fr" class="text-sm hover:underline" style="color: var(--accent);">contact@linscarbon.fr</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: var(--accent-light);">
                            <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.phone') }}</p>
                            <a href="tel:+33123456789" class="text-sm hover:underline" style="color: var(--accent);">+33 1 23 45 67 89</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: var(--accent-light);">
                            <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.address') }}</p>
                            <p class="text-sm" style="color: var(--text-secondary);">
                                123 Avenue de la Republique<br>
                                75011 Paris, France
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-10 p-6 rounded-xl" style="background-color: #f0fdfa;">
                    <h3 class="font-medium mb-2" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.hours') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        {{ __('linscarbon.marketing.contact.hours_weekdays') }}<br>
                        {{ __('linscarbon.marketing.contact.hours_premium') }}
                    </p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl p-8 border" style="border-color: var(--border);">
                <h2 class="text-xl font-semibold mb-6" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.send_message') }}</h2>

                <form action="#" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.form.name') }}</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2"
                               style="border-color: var(--border); focus:ring-color: var(--accent-light); focus:border-color: var(--accent);">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-1.5" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.form.email') }}</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2"
                               style="border-color: var(--border);">
                    </div>

                    <div>
                        <label for="company" class="block text-sm font-medium mb-1.5" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.form.company') }}</label>
                        <input type="text" id="company" name="company"
                               class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2"
                               style="border-color: var(--border);">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium mb-1.5" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.form.subject') }}</label>
                        <select id="subject" name="subject" required
                                class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2"
                                style="border-color: var(--border);">
                            <option value="">{{ __('linscarbon.marketing.contact.form.select_subject') }}</option>
                            <option value="demo">{{ __('linscarbon.marketing.contact.subjects.demo') }}</option>
                            <option value="pricing">{{ __('linscarbon.marketing.contact.subjects.pricing') }}</option>
                            <option value="enterprise">{{ __('linscarbon.marketing.contact.subjects.enterprise') }}</option>
                            <option value="partnership">{{ __('linscarbon.marketing.contact.subjects.partnership') }}</option>
                            <option value="support">{{ __('linscarbon.marketing.contact.subjects.support') }}</option>
                            <option value="other">{{ __('linscarbon.marketing.contact.subjects.other') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium mb-1.5" style="color: var(--text-primary);">{{ __('linscarbon.marketing.contact.form.message') }}</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 resize-none"
                                  style="border-color: var(--border);"></textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-sm font-medium text-white rounded-lg" style="background-color: var(--accent);">
                        {{ __('linscarbon.marketing.contact.form.send') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
