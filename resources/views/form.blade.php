@extends('layouts.app')

@section('content')
<div class="container">
    <form method="post" action={{ route('user/changePhoto') }} enctype="multipart/form-data">
        <input type="hidden" name="">
        <input type="text" name="uid" id="uid" placeholder='uid'>
        <br>
        <input type="file" name="contents" id="contents">
        <br>
        <button type="submit">Submit</button>
    </form>
</div>
@endsection
