@extends('layouts.app')

@section('title', 'Contact - Start Bootstrap Template')

@section('content')
<section class="py-5">
    <div class="container px-5">
        <div class="bg-light rounded-4 py-5 px-4 px-md-5">
            <div class="text-center mb-5">
                <div class="feature bg-primary bg-gradient-primary-to-secondary text-white rounded-3 mb-3"><i class="bi bi-envelope"></i></div>
                <h1 class="fw-bolder">Get in touch</h1>
                <p class="lead fw-normal text-muted mb-0">Let's work together!</p>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <form id="contactForm" data-sb-form-api-token="API_TOKEN">
                        <div class="form-floating mb-3">...</div>
                        <div class="form-floating mb-3">...</div>
                        <div class="form-floating mb-3">...</div>
                        <div class="form-floating mb-3">...</div>
                        <div class="d-none" id="submitSuccessMessage">...</div>
                        <div class="d-none" id="submitErrorMessage">...</div>
                        <div class="d-grid"><button class="btn btn-primary btn-lg disabled" id="submitButton" type="submit">Submit</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
@endsection