@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Detalle de la orden') }}</div>

                <div class="card-body p-2">

                    <div class="row">
                        <div class="col mb-3">
                            {{ $order->message }}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Id</p>
                            <p>{{ $order->id }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Estado</p>
                            <p>{{ $order->status->name }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Método de pao</p>
                            <p>{{ $order->payment_method }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Cliente</p>
                            <p>{{ $order->customer->name }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Email</p>
                            <p>{{ $order->customer->email }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Celular</p>
                            <p>{{ $order->customer->mobile }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Código / Referencia</p>
                            <p>{{ $order->code }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Request Id</p>
                            <p>{{ $order->request_id }}</p>
                        </div>
                        <div class="col mb-3">
                            <p class="small text-muted mb-1">Fecha</p>
                            <p>{{ $order->created_at }}</p>
                        </div>
                    </div>

                </div>    
            </div>
        </div>
    </div>
</div>
@endsection
