@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.classic.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-code fa-fw" aria-hidden="true"></i> {{ trans('installer_messages.environment.classic.title') }}
@endsection

@section('container')
    <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
        {!! csrf_field() !!}
        <textarea class="textarea" name="envConfig">{{ $envConfig }}</textarea>
        @if (!Session::has('message'))
            <div class="buttons buttons--right">
                <button class="button button--light" type="submit">
                    <i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>
                    {!! trans('installer_messages.environment.classic.save') !!}
                </button>
            </div>
        @endif
    </form>

    @if (!isset($environment['errors']) && Session::has('message'))
        <div class="buttons-container">
            <a class="button float-right" href="{{ route('LaravelInstaller::database') }}">
                <i class="fa fa-check fa-fw" aria-hidden="true"></i>
                {!! trans('installer_messages.environment.classic.install') !!}
                <i class="fa fa-angle-double-right fa-fw" aria-hidden="true"></i>
            </a>
        </div>
    @endif
@endsection
