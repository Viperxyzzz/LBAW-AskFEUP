@extends('layouts.app')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:50vh">
    <h1 class="error-code m-0">{{ $exception->getStatusCode() }}</h1>
    <h4>{{ $message }}</h4>
    <a href="{{ url()->previous() }}">Go back</a>
</div>
@endsection