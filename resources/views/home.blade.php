@extends('layouts.app')

@section('content')
    <x-categories-nav :currentCity="$currentCity ?? null" :currentCategory="$currentCategory ?? null"
                      :categories="$categories"/>

    <section class="position-relative overflow-hidden pt-1 mb-2">
        <div class="container position-relative pt-2 mt-sm-2">

            {{-- Форма поиска --}}
            @include('home.search')

            <!-- Featured events slider -->
            @include('home.slider')
            <span class="position-absolute z-1 fw-bold"
                  style="top: -15px; right: 100%; margin-right: -216px; font-size: 128px; color: var(--fn-tertiary-bg); text-shadow: 1px 1px 0 var(--fn-border-color), -1px 1px 0 var(--fn-border-color), -1px -1px 0 var(--fn-border-color), 1px -1px 0 var(--fn-border-color)">Кино</span>
            <span class="position-absolute z-1 fw-bold"
                  style="top: 82px; right: 100%; margin-right: -365px; font-size: 128px; color: var(--fn-tertiary-bg); text-shadow: 1px 1px 0 var(--fn-border-color), -1px 1px 0 var(--fn-border-color), -1px -1px 0 var(--fn-border-color), 1px -1px 0 var(--fn-border-color)">Концерты</span>
            <span class="position-absolute z-1 fw-bold"
                  style="top: 110px; left: 100%; margin-left: 90px; font-size: 128px; color: var(--fn-tertiary-bg); text-shadow: 1px 1px 0 var(--fn-border-color), -1px 1px 0 var(--fn-border-color), -1px -1px 0 var(--fn-border-color), 1px -1px 0 var(--fn-border-color)">Фестивали</span>
            <span class="position-absolute z-1 fw-bold"
                  style="top: 206px; left: 100%; margin-left: 10px; font-size: 128px; color: var(--fn-tertiary-bg); text-shadow: 1px 1px 0 var(--fn-border-color), -1px 1px 0 var(--fn-border-color), -1px -1px 0 var(--fn-border-color), 1px -1px 0 var(--fn-border-color)">Театр</span>
        </div>
        <span class="position-absolute top-0 start-0 w-100 bg-body-tertiary d-lg-none"
              style="height: calc(100% - 20px)"></span>
        <span class="position-absolute top-0 start-0 w-100 bg-body-tertiary d-none d-lg-block"
              style="height: calc(100% - 52px)"></span>
    </section>

    @include('home.result-search')

    @include('home.popular')

    @include('home.category')


@endsection

