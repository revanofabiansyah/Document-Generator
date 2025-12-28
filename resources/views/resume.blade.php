@extends('layouts.app')

@section('title', 'Resume - Start Bootstrap Template')

@section('body-class', 'bg-light')

@section('content')
<div class="container px-5 my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">Resume</span></h1>
    </div>
    <div class="row gx-5 justify-content-center">
        <div class="col-lg-11 col-xl-9 col-xxl-8">
            <section>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="text-primary fw-bolder mb-0">Experience</h2>
                    <a class="btn btn-primary px-4 py-3" href="#!">
                        <div class="d-inline-block bi bi-download me-2"></div>
                        Download Resume
                    </a>
                </div>
                <div class="card shadow border-0 rounded-4 mb-5">...</div>
                <div class="card shadow border-0 rounded-4 mb-5">...</div>
            </section>
            <section>
                <h2 class="text-secondary fw-bolder mb-4">Education</h2>
                <div class="card shadow border-0 rounded-4 mb-5">...</div>
                <div class="card shadow border-0 rounded-4 mb-5">...</div>
            </section>
            <div class="pb-5"></div>
            <section>
                <div class="card shadow border-0 rounded-4 mb-5">
                    <div class="card-body p-5">
                        <div class="mb-5">...</div>
                        <div class="mb-0">...</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection