@extends('web.layout.main')

@section('title')
{{ $moduleTitle ?? 'Manage Family' }}
@endsection

@section('styles')
@endsection

@section('content')
<section class="content">
    <div class="box">
        <div class="box-header align-right">
            <h1 class="box-title"> Welcome to Family Project!</h1>
        </div>
            
        <div class="box-body">
           <p>Go to <a href="{{ route('web.head.index') }}"> Family Head List</a>
           to view family head List.</p>
           <p>Go to <a href="{{ route('web.head.create') }}"> Add Family Head</a>
           to create family head.</p>
           <p>Go to <a href="{{ route('web.head.member.create') }}"> Add Family Member</a>
           to create family member after creating family head.</p>

        </div>
    </div>
</section>
   
@endsection
