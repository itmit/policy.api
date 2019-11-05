@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header ">Внимание</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Добро пожаловать!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
