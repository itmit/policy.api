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

                <div class="form-group{{ $errors->has('subcategory') ? ' has-error' : '' }}">
                    <select name="subcategory" id="subcategory" class="form-control">
                        <option value="" selected>Нет</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('subcategory'))
                        <span class="help-block">
                            <strong>{{ $errors->first('subcategory') }}</strong>
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

<script>
    $(document).ready(function()
    {
        $(document).on('change', 'select[name="subcategory"]', function() {
            let subcategory = $('select[name="subcategory"]').val();
            if(subcategory != null)
            {
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {subcategory: subcategory},
                url     : 'getSubcategories',
                method    : 'post',
                success: function (data) {
                    console.log(data);
                },
                error: function (xhr, err) { 
                    console.log(err + " " + xhr);
                }
            });
            }
        });
    });
</script>

@endsection