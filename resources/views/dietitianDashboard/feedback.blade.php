@extends('dietitianDashboard.layout')
@section('content')
  @include('components.feedback-table', ['items' => $items, 'title' => 'Feedback I Received', 'theme' => 'dietitian'])
@endsection
