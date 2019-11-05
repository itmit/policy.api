@extends('layouts.adminApp')

@section('content') 
<div class="col-sm-9 tabs-content">
   <h1>Создание категории</h1>
    <div class="row">
        <div class="col-sm-12">
           <p class="title-poll">Уже созданные категории:</p>
            <div class="textareaPoll" name="listOfCategories" cols="20" rows="10" disabled style="resize: none;">
                @foreach ($categories as $category)
                <div class="category-item">
                {{ $category->name }}
                </div>
                @endforeach
            </div>
            <div class="title-poll">Создать категорию:</div>
            <form class="form-horizontal" method="POST" action="{{ route('auth.storeCategory') }}">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input id="name" type="text" class="form-control input-create-poll" name="name" value="{{ old('name') }}" placeholder="Наименование" required autofocus>
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-offset-4">
                        <button type="submit" class="btn-card">
                            Создать категорию
                        </button>
                    </div>
                </div>
            </form>
        </div>  
    </div> 
</div> 
@endsection