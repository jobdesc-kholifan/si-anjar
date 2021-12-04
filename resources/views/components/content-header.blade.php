<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $title }}</h1>
                <small>{{ $subTitle }}</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}">
                            <span class="oi oi-home"></span>
                            Dashboard
                        </a>
                    </li>
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item{{$isBreadcrumbActive($breadcrumb)}}">
                            @if(!empty($breadcrumb['link']))
                                <a href="{{$breadcrumb['link']}}">{{ $breadcrumbLabel($breadcrumb) }}</a>
                            @else
                                {{ $breadcrumbLabel($breadcrumb) }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
