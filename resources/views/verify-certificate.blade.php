<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <style>
      .wf-force-outline-none[tabindex="-1"]:focus {
        outline: none;
      }
    </style>
    <meta charset="utf-8" />
    <title>Certificate Verification</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link
      href={{ URL::asset('public/main.css'); }} 
      rel="stylesheet"
      type="text/css"
    />
    <link rel="icon" href="#" sizes="32x32">
    <link rel="icon" href="#" sizes="192x192">
    <link rel="apple-touch-icon" href="#">
  </head>
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <body>
    <div class="section wf-section">
      <img
        src="images/TUV Austria Logo.png"
        loading="lazy"
        alt=""
        class="image"
        width="250"
      />
      <h1 class="heading">Verify Training Certificate</h1>
      <p class="paragraph">
        Enter the Certificate Number and click the "Verify"&nbsp;button.
      </p>
      <div class="form-block w-form">
        <form
          id="s-form"
          name="s-form"
          method="get"
          class="form"
          aria-label="Search Form"
        >
          <input
            type="text"
            class="text-field w-input"
            maxlength="256"
            name="search"
            placeholder="Ex: TUV/CERT/2022/0911/001"
            id="search"
            required=""
          /><input
            type="submit"
            value="VERIFY"
            data-wait="Please wait..."
            class="submit-button w-button"
          />
        </form>
        <div
          class="w-form-done"
          tabindex="-1"
          role="region"
          aria-label="Form success"
        >
          <div>Thank you! Your submission has been received!</div>
        </div>
        <div
          class="w-form-fail"
          tabindex="-1"
          role="region"
          aria-label="Email Form failure"
        >
          <div>Oops! Something went wrong while submitting the form.</div>
        </div>
      </div>
      @isset($certificates)
      <div>
      @if($certificates->count() < 1)
        <h3>⚠️ The Certificate You Entered is Invalid or Manipulated. Please contact TUV Austria for futher inquiry. ⚠️</h3>
      @endif
      {{-- @foreach ($certificates as $certificate)
        <h3>Certificate Authentic! ✅</h3>
        <h3>Certificate Number:&nbsp;{{ $certificate->certificate_number }}</h3>
        <h3>Participant Name:&nbsp;{{ $certificate->participant_name }}</h3>
        <h3>Passport/NID:&nbsp;{{ $certificate->passport_nid }}</h3>
        <h3>Driving License:&nbsp;{{ $certificate->driving_license }}</h3>
        <h3>Company:&nbsp;{{ $certificate->company }}</h3>
        <h3>Training:&nbsp;{{ $certificate->training_name }}</h3>
        <h3>Training Location:&nbsp;{{ $certificate->location }}</h3>
        <h3>Trainer:&nbsp;{{ $certificate->trainer }}</h3>
        <h3>Training Date:&nbsp;{{ $certificate->training_date }}</h3>
        <h3>Issue Date:&nbsp;{{ $certificate->issue_date }}</h3>
        <h3>Expiry Date:&nbsp;{{ $certificate->expiry_date }}</h3>
      @endforeach --}}
      {{-- Table format implemented below --}}
      @foreach ($certificates as $certificate)
          <div>
              @if (\Carbon\Carbon::parse($certificate->expiry_date)->isPast())
                  <h3 style="color: red;">Certificate Authentic but Expired! ⚠️</h3>
              @else
                  <h3 style="color: green;">Certificate Authentic and Valid! ✅</h3>
              @endif
              <br>
              <table style="width: 100%; border-collapse: collapse;">
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Certificate Number</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->certificate_number }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Participant Name</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->participant_name }}</h3></td>
                  </tr>
                  {{-- NID and DL info removed from public display for privacy reasons. --}}
                  {{-- <tr>
                      <td style="padding: 6px;"><h3><strong>Passport/NID</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->passport_nid }}</h3></td>
                  </tr> --}}
                  {{-- <tr>
                      <td style="padding: 6px;"><h3><strong>Driving License</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->driving_license }}</h3></td>
                  </tr> --}}
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Company</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->company }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Training</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->training_name }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Training Location</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->location }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Trainer</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->trainer }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Training Start Date</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->training_date }}</h3></td>
                  </tr>
                  <tr>
                    <td style="padding: 6px;"><h3><strong>Training End Date</strong></h3></td>
                    <td style="padding: 6px;"><h3>:</h3></td>
                    <td style="padding: 6px;"><h3>{{ $certificate->training_end }}</h3></td>
                </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Issue Date</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->issue_date }}</h3></td>
                  </tr>
                  <tr>
                      <td style="padding: 6px;"><h3><strong>Expiry Date</strong></h3></td>
                      <td style="padding: 6px;"><h3>:</h3></td>
                      <td style="padding: 6px;"><h3>{{ $certificate->expiry_date }}</h3></td>
                  </tr>
              </table>
          </div>
      @endforeach

      </div>
      @endisset
    </div>
    @include('layouts.footer')  <!-- Including the footer Blade file -->
  </body>
</html>
