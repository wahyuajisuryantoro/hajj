@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jstree/jstree.css') }}" />
@endsection

@section('content')
    <div class="col-md-12 col-12">
        <div class="card mb-12">
            <h5 class="card-header">{{ $title }}</h5>
            <div class="card-body">
                <div id="jstree-mitra"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendor/libs/jstree/jstree.js') }}"></script>
    <script>
        $(function() {
            var theme = $('html').hasClass('light-style') ? 'default' : 'default-dark';
            var mitraTree = $('#jstree-mitra');

            if (mitraTree.length) {
                mitraTree.jstree({
                    core: {
                        themes: {
                            name: theme
                        },
                        data: @json($tree)
                    },
                    plugins: ['types'],
                    types: {
                        'default': {
                            'icon': false
                        },
                        'leader': {
                            'icon': false
                        }
                    }
                }).on('ready.jstree', function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });
            }
        });
    </script>
@endsection