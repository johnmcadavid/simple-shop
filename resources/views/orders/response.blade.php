@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Nueva orden') }}</div>

                <div class="card-body p-2">

                    <div class="mb-3">
                        RequestId: {{ $requestId }}
                    </div>
                    <div class="mb-3">
                        Reference: {{ $reference }}
                    </div>
                    <div class="mb-3">
                        Signature: {{ $signature }}
                    </div>

                </div>    
            </div>
        </div>
    </div>
</div>
@endsection
