@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-classifieds @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('classifieds.classifieds')</h2><a class="btn btn--orange" href="{{ route('classifieds.create') }}" role="button">Add a New Classified</a>
        </div>
        @if($classifieds->count())
            <div class="overflow-auto">
                <table class="dashboard-table table">
                    <thead>
                    <tr>
                        <th class="no-wrap" scope="col" width="1"></th>
                        <th scope="col"></th>
                        <th class="no-wrap" scope="col" width="1">Price</th>
                        <th class="no-wrap" scope="col" width="1">State</th>
                        <th class="no-wrap"scope="col" width="1">Type</th>
                        <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($classifieds as $classified)
                        <tr>
                            <td>
                                <img src="{{ $classified->getThumb('120x120') }}" alt="{{ $classified->title }}">
                            </td>
                            <td>
                                @php($views = $classified->getViews())
                                <div class="views-count">{{ trans_choice('general.views_count', $views, ['value' => $views]) }}</div>
                                <h3><a href="{{ route('classifieds.edit', $classified->id) }}">{{ $classified->title }}</a></h3>
                                <span class="category"><small>{{ $classified->category->title }}</small></span>
                                <div>
                                    {!! HtmlTruncator::truncate($classified->description, 52) !!}
                                </div>
                            </td>
                            <td class="no-wrap">
                                {{ $classified->priceLabel }}
                            </td>
                            <td class="no-wrap">
                                {{ $classified->stateLabel }}
                            </td>
                            <td class="no-wrap">
                                {{ $classified->typeLabel }}
                            </td>
                            <td class="actions">
                                <a href="{{ route('classifieds.show', ['slug' => $classified->slug, 'category_slug' => $classified->category->slug]) }}" class="btn" target="_blank">View</a>
                                <a href="{{ route('classifieds.edit', $classified->id) }}" class="btn">Edit</a>
                                <a href="{{ route('classifieds.archive', $classified->id) }}" onclick="return confirm('Are you sure to archive the &quot;'+ $(this).data('title') +'&quot; classified?')" class="btn" data-title="{{ $classified->title }}">Deactivate</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $classifieds->links() }}
        @else
            <div class="alert alert-info">@lang('classifieds.no_classified')</div>
        @endif
    </div>
@endsection