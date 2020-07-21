@extends('admin/layouts/default')

{{-- Web site Title --}}
@section('title')
    @lang('billing.plans') @parent
@stop

{{-- Content --}}
@section('content')
<section class="content-header">
    <h1>@lang('billing.plans')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li>@lang('billing.billing')</li>
        <li class="active">@lang('billing.plans')</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card panel-primary ">
                <div class="card-heading clearfix">
                    <h4 class="card-title"> <i class="livicon" data-name="list-ul" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        @lang('billing.plans')
                    </h4>
                </div>
                <div class="card-body">
                    @if ($plans->count() >= 1)
                        <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive ">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Braintree Plan</th>
                                        <th>Cost</th>
                                        <th>Currency</th>
                                        <th>Frequency</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->name }}</td>
                                        <td>{{ $plan->slug }}</td>
                                        <td>{{ $plan->braintree_plan }}</td>
                                        <td class="text-right">{{ $plan->cost }}</td>
                                        <td>{{ $plan->currency }}</td>
                                        <td>{{ PlanHelper::getFrequencyLabel($plan->billing_frequency) }}</td>
                                        <td>{{ $plan->created_at->toFormattedDateString() }}</td>
                                        <td>{{ $plan->updated_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @lang('general.noresults')
                    @endif   
                </div>
            </div>
        </div>
    </div><!-- row-->
</section>
@stop
