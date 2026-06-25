@extends('layouts.public')

@section('content')
<div class="home">
  <section class="contact" style="max-width:640px;margin:80px auto">
    <div class="contact__head" style="margin-bottom:40px">
      <div class="eyebrow eyebrow--accent">{{ __('contact.eyebrow') }}</div>
      <h1 class="contact__title" style="font-family:var(--font-display);font-size:56px;line-height:1;letter-spacing:-0.02em;margin:8px 0 0">{{ __('contact.title') }}</h1>
      <h2 class="contact__h2" style="font-family:var(--font-display);font-size:40px;line-height:1.05;color:var(--text-muted);margin:8px 0 0">{{ __('contact.h2') }}</h2>
      <p class="contact__sub" style="font-size:14px;color:var(--text-muted);margin-top:16px">{{ __('contact.sub') }}</p>
    </div>

    @if (session('success'))
      <div class="contact__alert contact__alert--ok" style="padding:16px 20px;background:var(--risk-min-bg);border:1px solid var(--risk-min);border-radius:var(--r-md);margin-bottom:24px;font-size:14px;color:var(--text)">{{ session('success') }}</div>
    @endif

    <form class="contact__card" method="POST" action="{{ route('contact.store') }}" style="display:flex;flex-direction:column;gap:16px">
      @csrf

      <div style="position:absolute;left:-9999px" aria-hidden="true">
        <label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
      </div>

      <label class="label">{{ __('contact.name') }}*</label>
      <input class="input" type="text" name="name" value="{{ old('name') }}" required>
      @error('name')<p class="contact__err" style="font-size:12px;color:var(--risk-haut);margin:0"> {{ $message }}</p>@enderror

      <label class="label">{{ __('contact.email') }}*</label>
      <input class="input" type="email" name="email" value="{{ old('email') }}" required>
      @error('email')<p class="contact__err" style="font-size:12px;color:var(--risk-haut);margin:0"> {{ $message }}</p>@enderror

      <label class="label">{{ __('contact.message') }}*</label>
      <textarea class="input textarea" name="message" required>{{ old('message') }}</textarea>
      @error('message')<p class="contact__err" style="font-size:12px;color:var(--risk-haut);margin:0"> {{ $message }}</p>@enderror

      <label class="label">{{ __('contact.captcha', ['a' => $a, 'b' => $b]) }}</label>
      <input class="input" type="text" name="captcha" inputmode="numeric" placeholder="{{ __('contact.captcha_ph') }}" required>
      @error('captcha')<p class="contact__err" style="font-size:12px;color:var(--risk-haut);margin:0"> {{ $message }}</p>@enderror

      <label class="contact__consent" style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--text-muted);line-height:1.5;margin-top:8px">
        <input type="checkbox" name="consent" value="1" required style="margin-top:2px">
        <span>{{ __('contact.consent') }}</span>
      </label>

      <button type="submit" class="btn btn--accent btn--lg" style="align-self:flex-start;margin-top:8px">{{ __('contact.submit') }}</button>
      <p class="contact__note" style="font-size:11px;color:var(--text-dim)">{{ __('contact.note') }}</p>
    </form>
  </section>
</div>
@endsection
