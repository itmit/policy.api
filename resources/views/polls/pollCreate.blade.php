@extends('layouts.adminApp')

@section('content')
    <h1>Создание опроса</h1>
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.polls.store') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Наименование</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                           autofocus>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('radio') ? ' has-error' : '' }}">
                <div class="col-md-6">
                    <input type="radio" id="unlimited" name="time" value="unlimited" checked>
                    <label for="unlimited">Бессрочно</label>

                    <input type="radio" id="limited" name="time" value="limited">
                    <label for="limited">с датой начала и завершения</label>
                </div>
            </div>

            <div class="form-group{{ $errors->has('start_at') ? ' has-error' : '' }}">
                <label for="start_at" class="col-md-4 control-label">Дата начала</label>

                <div class="col-md-6">
                    <input id="start_at" type="date" class="form-control" name="start_at" value="{{ old('start_at') }}"
                           autofocus disabled>

                    @if ($errors->has('start_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('start_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('end_at') ? ' has-error' : '' }}">
                <label for="end_at" class="col-md-4 control-label">Дата завершения</label>

                <div class="col-md-6">
                    <input id="end_at" type="date" class="form-control" name="end_at" value="{{ old('end_at') }}"
                           autofocus disabled>

                    @if ($errors->has('end_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('end_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Создать опрос
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $(document).on('change', $('input:radio[name=time]'), function() {
            console.log($('input:radio[name=time]:checked').val());
        });
    })
    </script>
    
@endsection