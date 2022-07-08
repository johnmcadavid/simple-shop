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

                    @if ($order->status->name == "PAYED")
                        <div class="alert alert-success text-center" role="alert">
                            {{ $order->message }}
                        </div>                        
                    @endif
                    @if ($order->status->name == "REJECTED")
                        <div class="alert alert-danger text-center" role="alert">
                            {{ $order->message }}
                        </div>
                        <div class="text-center">
                            <input type="button" onclick="location.href='/orders/create/customer/{{ $order->customer_id }}';" value="Reintentar pago" />
                        </div>
                    @endif
                    @if (trim($order->status->name) == "CREATED")
                        <div class="alert alert-warning text-center" role="alert">
                            {{ $order->message }}
                        </div>
                        <div class="text-center">
                            <input type="button" onclick="location.href='{{ $order->process_url }}';" value="Retomar pago" />
                        </div>
                    @endif

                </div>    
            </div>
        </div>
    </div>
</div>
@endsection
