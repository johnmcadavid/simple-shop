@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Lista de órdenes') }}</div>

                <div class="container col-md-offset-2">
                    <div class="panel panel-default">
                        @if ($orders->isEmpty())
                            <div>No se encontraron órdenes registradas</div>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Email</th>
                                        <th>Celular</th>
                                        <th>Código/Referencia</th>
                                        <th>Estado</th>
                                        <th>RequestId</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{!! $order->id !!}</td>
                                            <td>{!! $order->customer->created_at !!}</td>
                                            <td>{!! $order->customer->name !!}</td>
                                            <td>{!! $order->customer->email !!}</td>
                                            <td>{!! $order->customer->mobile !!}</td>
                                            <td>{!! $order->code !!}</td>
                                            <td>{!! $order->status->name !!}</td>
                                            <td title="{!! $order->message !!}">{!! $order->request_id !!}</td>
                                            <td><a href="/orders/response/reference/{{$order->code}}">Ver</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection
