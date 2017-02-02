@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ trans("action.{$mode}") }}} {{ trans('idmkr/adwords::generationfeeditemadgroups/common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('page')

<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="adwords-form" action="{{ request()->fullUrl() }}" role="form" method="post" data-parsley-validate>

		{{-- Form: CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<header class="panel-heading">

			<nav class="navbar navbar-default navbar-actions">

				<div class="container-fluid">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.idmkr.adwords.generationfeeditemadgroups.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $generationfeeditemadgroup->exists ? $generationfeeditemadgroup->id : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($generationfeeditemadgroup->exists)
							<li>
								<a href="{{ route('admin.idmkr.adwords.generationfeeditemadgroups.delete', $generationfeeditemadgroup->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>
							@endif

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<div class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::generationfeeditemadgroups/common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::generationfeeditemadgroups/common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>

							<div class="row">

								<div class="form-group{{ Alert::onForm('feed_item_id', ' has-error') }}">

									<label for="feed_item_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.feed_item_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.feed_item_id') }}}
									</label>

									<input type="text" class="form-control" name="feed_item_id" id="feed_item_id" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.feed_item_id') }}}" value="{{{ input()->old('feed_item_id', $generationfeeditemadgroup->feed_item_id) }}}">

									<span class="help-block">{{{ Alert::onForm('feed_item_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('generation_id', ' has-error') }}">

									<label for="generation_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.generation_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.generation_id') }}}
									</label>

									<input type="text" class="form-control" name="generation_id" id="generation_id" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.generation_id') }}}" value="{{{ input()->old('generation_id', $generationfeeditemadgroup->generation_id) }}}">

									<span class="help-block">{{{ Alert::onForm('generation_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adwords_adgroup_id', ' has-error') }}">

									<label for="adwords_adgroup_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_id') }}}
									</label>

									<input type="text" class="form-control" name="adwords_adgroup_id" id="adwords_adgroup_id" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_id') }}}" value="{{{ input()->old('adwords_adgroup_id', $generationfeeditemadgroup->adwords_adgroup_id) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_adgroup_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adwords_adgroup_status', ' has-error') }}">

									<label for="adwords_adgroup_status" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_status_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_status') }}}
									</label>

									<input type="text" class="form-control" name="adwords_adgroup_status" id="adwords_adgroup_status" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_status') }}}" value="{{{ input()->old('adwords_adgroup_status', $generationfeeditemadgroup->adwords_adgroup_status) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_adgroup_status') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adwords_adgroup_ads_count', ' has-error') }}">

									<label for="adwords_adgroup_ads_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_ads_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_ads_count') }}}
									</label>

									<input type="text" class="form-control" name="adwords_adgroup_ads_count" id="adwords_adgroup_ads_count" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_ads_count') }}}" value="{{{ input()->old('adwords_adgroup_ads_count', $generationfeeditemadgroup->adwords_adgroup_ads_count) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_adgroup_ads_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adwords_adgroup_keywords_count', ' has-error') }}">

									<label for="adwords_adgroup_keywords_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_keywords_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_keywords_count') }}}
									</label>

									<input type="text" class="form-control" name="adwords_adgroup_keywords_count" id="adwords_adgroup_keywords_count" placeholder="{{{ trans('idmkr/adwords::generationfeeditemadgroups/model.general.adwords_adgroup_keywords_count') }}}" value="{{{ input()->old('adwords_adgroup_keywords_count', $generationfeeditemadgroup->adwords_adgroup_keywords_count) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_adgroup_keywords_count') }}}</span>

								</div>


							</div>

						</fieldset>

					</div>

					{{-- Tab: Attributes --}}
					<div role="tabpanel" class="tab-pane fade" id="attributes">
						@attributes($generationfeeditemadgroup)
					</div>

				</div>

			</div>

		</div>

	</form>

</section>
@stop
