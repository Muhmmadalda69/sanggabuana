@extends('layouts.app')

@section('title', $page->title . ' - Wisata Sanggabuana')
@section('meta_description', $page->meta_description)

@section('content')
<div class="pt-28 pb-16 bg-forest-950 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-repeat bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-up">{{ $page->title }}</h1>
        <div class="w-20 h-2 bg-forest-500 rounded-full mx-auto mb-6 animate-fade-up" style="animation-delay: 0.1s"></div>
    </div>
</div>

<div class="py-16 bg-white min-h-[50vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-forest prose-lg max-w-none prose-headings:text-forest-950 prose-a:text-forest-600 hover:prose-a:text-forest-800 animate-fade-up">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
