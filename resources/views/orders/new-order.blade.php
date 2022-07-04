@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Nueva orden') }}</div>

                <div class="card-body p-2">

                    @if(Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                        @php
                            Session::forget('success');
                        @endphp
                    </div>
                    @endif

                    <!-- Display Error Message -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.
                            <!--
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            -->
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/orders/new-order') }}">
                        {{ csrf_field() }}

                        <div class="mb-3">
                            <label class="form-label" for="inputName">Name:</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="inputName"
                                class="form-control @error('name') is-invalid @enderror" 
                                placeholder="Name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="inputEmail">Email:</label>
                            <input 
                                type="text" 
                                name="email" 
                                id="inputEmail"
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="Email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="inputName">Mobile:</label>
                            <input 
                                type="text" 
                                name="mobile" 
                                id="inputMobile"
                                class="form-control @error('mobile') is-invalid @enderror" 
                                placeholder="Mobile">
                            @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-success btn-submit">Submit</button>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>
</div>
@endsection
