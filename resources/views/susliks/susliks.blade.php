@extends('layouts.adminApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('auth.susliks.create') }}" class="btn btn-primary">Создать суслика</a>
            <a href="{{ route('auth.createCategory') }}" class="btn btn-primary">Создать категорию</a>
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('auth.uploadSusliks') }}">
                {{ csrf_field() }}
                <div class="col-sm-12">
                    <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                        <label for="file" class="col-md-4 control-label text-tc">.xlsx файл для импорта</label>
            
                        <div class="col-md-6">
                            <input type="file" name="file" id="file" accept=".csv">
                        </div>
            
                        @if ($errors->has('file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                    </div>
            
                    <div class="form-group">
                        <button type="submit" class="btn btn-tc-ct">
                                Загрузить сусликов из csv-файла
                        </button>
                    </div>
                </div>
            </form>
            
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Категория</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody>
                @foreach($susliks as $suslik)
                    <tr>
                        <td><a href="suslik/{{ $suslik->id }}"> {{ $suslik->name }} </a></td>
                        <td>{{ $suslik->category()->name }}</td>
                        <td>{{ $suslik->created_at->timezone('Europe/Moscow') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
