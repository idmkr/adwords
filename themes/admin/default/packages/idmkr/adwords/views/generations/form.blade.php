@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ trans("action.{$mode}") }}} {{ trans('idmkr/adwords::generations/common.title') }}
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

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.idmkr.adwords.generations.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $generation->exists ? $generation->id : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($generation->exists)
							<li>
								<a href="{{ route('admin.idmkr.adwords.generations.delete', $generation->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
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
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::generations/common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::generations/common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>

							<div class="row">

								<div class="form-group{{ Alert::onForm('adwords_batch_job_id', ' has-error') }}">

									<label for="adwords_batch_job_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.adwords_batch_job_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.adwords_batch_job_id') }}}
									</label>

									<input type="text" class="form-control" name="adwords_batch_job_id" id="adwords_batch_job_id" placeholder="{{{ trans('idmkr/adwords::generations/model.general.adwords_batch_job_id') }}}" value="{{{ input()->old('adwords_batch_job_id', $generation->adwords_batch_job_id) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_batch_job_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('templategroupeannonce_id', ' has-error') }}">

									<label for="templategroupeannonce_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.templategroupeannonce_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.templategroupeannonce_id') }}}
									</label>

									<input type="text" class="form-control" name="templategroupeannonce_id" id="templategroupeannonce_id" placeholder="{{{ trans('idmkr/adwords::generations/model.general.templategroupeannonce_id') }}}" value="{{{ input()->old('templategroupeannonce_id', $generation->templategroupeannonce_id) }}}">

									<span class="help-block">{{{ Alert::onForm('templategroupeannonce_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('feed_id', ' has-error') }}">

									<label for="feed_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.feed_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.feed_id') }}}
									</label>

									<input type="text" class="form-control" name="feed_id" id="feed_id" placeholder="{{{ trans('idmkr/adwords::generations/model.general.feed_id') }}}" value="{{{ input()->old('feed_id', $generation->feed_id) }}}">

									<span class="help-block">{{{ Alert::onForm('feed_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adwords_feed_id', ' has-error') }}">

									<label for="adwords_feed_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.adwords_feed_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.adwords_feed_id') }}}
									</label>

									<input type="text" class="form-control" name="adwords_feed_id" id="adwords_feed_id" placeholder="{{{ trans('idmkr/adwords::generations/model.general.adwords_feed_id') }}}" value="{{{ input()->old('adwords_feed_id', $generation->adwords_feed_id) }}}">

									<span class="help-block">{{{ Alert::onForm('adwords_feed_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('operations_count', ' has-error') }}">

									<label for="operations_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.operations_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.operations_count') }}}
									</label>

									<input type="text" class="form-control" name="operations_count" id="operations_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.operations_count') }}}" value="{{{ input()->old('operations_count', $generation->operations_count) }}}">

									<span class="help-block">{{{ Alert::onForm('operations_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('status', ' has-error') }}">

									<label for="status" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.status_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.status') }}}
									</label>

									<input type="text" class="form-control" name="status" id="status" placeholder="{{{ trans('idmkr/adwords::generations/model.general.status') }}}" value="{{{ input()->old('status', $generation->status) }}}">

									<span class="help-block">{{{ Alert::onForm('status') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('ended_at', ' has-error') }}">

									<label for="ended_at" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.ended_at_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.ended_at') }}}
									</label>

									<input type="text" class="form-control" name="ended_at" id="ended_at" placeholder="{{{ trans('idmkr/adwords::generations/model.general.ended_at') }}}" value="{{{ input()->old('ended_at', $generation->ended_at) }}}">

									<span class="help-block">{{{ Alert::onForm('ended_at') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('adgroups_count', ' has-error') }}">

									<label for="adgroups_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.adgroups_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.adgroups_count') }}}
									</label>

									<input type="text" class="form-control" name="adgroups_count" id="adgroups_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.adgroups_count') }}}" value="{{{ input()->old('adgroups_count', $generation->adgroups_count) }}}">

									<span class="help-block">{{{ Alert::onForm('adgroups_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('spare_ads_count', ' has-error') }}">

									<label for="spare_ads_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.spare_ads_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.spare_ads_count') }}}
									</label>

									<input type="text" class="form-control" name="spare_ads_count" id="spare_ads_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.spare_ads_count') }}}" value="{{{ input()->old('spare_ads_count', $generation->spare_ads_count) }}}">

									<span class="help-block">{{{ Alert::onForm('spare_ads_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('customized_ads_count', ' has-error') }}">

									<label for="customized_ads_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.customized_ads_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.customized_ads_count') }}}
									</label>

									<input type="text" class="form-control" name="customized_ads_count" id="customized_ads_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.customized_ads_count') }}}" value="{{{ input()->old('customized_ads_count', $generation->customized_ads_count) }}}">

									<span class="help-block">{{{ Alert::onForm('customized_ads_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('keywords_count', ' has-error') }}">

									<label for="keywords_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.keywords_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.keywords_count') }}}
									</label>

									<input type="text" class="form-control" name="keywords_count" id="keywords_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.keywords_count') }}}" value="{{{ input()->old('keywords_count', $generation->keywords_count) }}}">

									<span class="help-block">{{{ Alert::onForm('keywords_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('feed_updates_count', ' has-error') }}">

									<label for="feed_updates_count" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.feed_updates_count_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.feed_updates_count') }}}
									</label>

									<input type="text" class="form-control" name="feed_updates_count" id="feed_updates_count" placeholder="{{{ trans('idmkr/adwords::generations/model.general.feed_updates_count') }}}" value="{{{ input()->old('feed_updates_count', $generation->feed_updates_count) }}}">

									<span class="help-block">{{{ Alert::onForm('feed_updates_count') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('enabled', ' has-error') }}">

									<label for="enabled" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::generations/model.general.enabled_help') }}}"></i>
										{{{ trans('idmkr/adwords::generations/model.general.enabled') }}}
									</label>

									<div class="checkbox">
										<label>
											<input type="hidden" name="enabled" id="enabled" value="0" checked>
											<input type="checkbox" name="enabled" id="enabled" @if($generation->enabled) checked @endif value="1"> {{ ucfirst('enabled') }}
										</label>
									</div>

									<span class="help-block">{{{ Alert::onForm('enabled') }}}</span>

								</div>


							</div>

						</fieldset>

					</div>

					{{-- Tab: Attributes --}}
					<div role="tabpanel" class="tab-pane fade" id="attributes">
						@attributes($generation)
					</div>

				</div>

			</div>

		</div>

	</form>

</section>
@stop
