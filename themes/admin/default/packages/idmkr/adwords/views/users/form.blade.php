@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ trans("action.{$mode}") }}} {{ trans('idmkr/adwords::users/common.title') }}
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

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.idmkr.adwords.users.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $user->exists ? $user->id : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($user->exists)
							<li>
								<a href="{{ route('admin.idmkr.adwords.users.delete', $user->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
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
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::users/common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab">{{{ trans('idmkr/adwords::users/common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>

							<div class="row">

								<div class="form-group{{ Alert::onForm('client_manager_id', ' has-error') }}">

									<label for="client_manager_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::users/model.general.client_manager_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::users/model.general.client_manager_id') }}}
									</label>

									<input type="text" class="form-control" name="client_manager_id" id="client_manager_id" placeholder="{{{ trans('idmkr/adwords::users/model.general.client_manager_id') }}}" value="{{{ input()->old('client_manager_id', $user->client_manager_id) }}}">

									<span class="help-block">{{{ Alert::onForm('client_manager_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('user_id', ' has-error') }}">

									<label for="user_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::users/model.general.user_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::users/model.general.user_id') }}}
									</label>

									<input type="text" class="form-control" name="user_id" id="user_id" placeholder="{{{ trans('idmkr/adwords::users/model.general.user_id') }}}" value="{{{ input()->old('user_id', $user->user_id) }}}">

									<span class="help-block">{{{ Alert::onForm('user_id') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('client_customer_id', ' has-error') }}">

									<label for="client_customer_id" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('idmkr/adwords::users/model.general.client_customer_id_help') }}}"></i>
										{{{ trans('idmkr/adwords::users/model.general.client_customer_id') }}}
									</label>

									<input type="text" class="form-control" name="client_customer_id" id="client_customer_id" placeholder="{{{ trans('idmkr/adwords::users/model.general.client_customer_id') }}}" value="{{{ input()->old('client_customer_id', $user->client_customer_id) }}}">

									<span class="help-block">{{{ Alert::onForm('client_customer_id') }}}</span>

								</div>


							</div>

						</fieldset>

					</div>

					{{-- Tab: Attributes --}}
					<div role="tabpanel" class="tab-pane fade" id="attributes">
						@attributes($user)
					</div>

				</div>

			</div>

		</div>

	</form>

</section>
@stop
