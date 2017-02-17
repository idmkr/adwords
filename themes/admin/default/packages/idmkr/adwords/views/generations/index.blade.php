@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{ trans('idmkr/adwords::generations/common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/css/daterangepicker-bs3.css', 'style') }}

{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('index', 'idmkr/adwords::generations/js/index.js', 'platform') }}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/js/daterangepicker.js', 'jquery') }}

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

{{-- Grid --}}
<section class="panel panel-default panel-grid">

	{{-- Grid: Header --}}
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

					<span class="navbar-brand">{{{ trans('idmkr/adwords::generations/common.title') }}}</span>

				</div>

				{{-- Grid: Actions --}}
				<div class="collapse navbar-collapse" id="actions">

					<ul class="nav navbar-nav navbar-left">

						<li class="disabled">
							<a class="disabled" data-grid-bulk-action="disable" data-toggle="tooltip" data-original-title="{{{ trans('action.bulk.disable') }}}">
								<i class="fa fa-eye-slash"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.disable') }}}</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="enable" data-toggle="tooltip" data-original-title="{{{ trans('action.bulk.enable') }}}">
								<i class="fa fa-eye"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.enable') }}}</span>
							</a>
						</li>

						<li class="danger disabled">
							<a data-grid-bulk-action="delete" data-toggle="tooltip" data-target="modal-confirm" data-original-title="{{{ trans('action.bulk.delete') }}}">
								<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.delete') }}}</span>
							</a>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="{{{ trans('action.export') }}}">
								<i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('action.export') }}}</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a data-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
								<li><a data-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
								<li><a data-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
							</ul>
						</li>

						<li class="primary">
							<a href="{{ route('admin.idmkr.adwords.generations.create') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.create') }}}">
								<i class="fa fa-plus"></i> <span class="visible-xs-inline">{{{ trans('action.create') }}}</span>
							</a>
						</li>

					</ul>

					{{-- Grid: Filters --}}
					<form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-search data-grid="generation" role="form">

						<div class="input-group">

							<span class="input-group-btn">

								<button class="btn btn-default" type="button" disabled>
									{{{ trans('common.filters') }}}
								</button>

								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>

								<ul class="dropdown-menu" role="menu">

									<li>
										<a data-grid="generation" data-filter="enabled:1" data-label="enabled::{{{ trans('common.all_enabled') }}}" data-reset>
											<i class="fa fa-eye"></i> {{{ trans('common.show_enabled') }}}
										</a>
									</li>

									<li>
										<a data-toggle="tooltip" data-placement="top" data-original-title="" data-grid="generation" data-filter="enabled:0" data-label="enabled::{{{ trans('common.all_disabled') }}}" data-reset>
											<i class="fa fa-eye-slash"></i> {{{ trans('common.show_disabled') }}}
										</a>
									</li>

									<li class="divider"></li>

									<li>
										<a data-grid-calendar-preset="day">
											<i class="fa fa-calendar"></i> {{{ trans('date.day') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="week">
											<i class="fa fa-calendar"></i> {{{ trans('date.week') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="month">
											<i class="fa fa-calendar"></i> {{{ trans('date.month') }}}
										</a>
									</li>

								</ul>

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="created_at">
									<i class="fa fa-calendar"></i>
								</button>

							</span>

							<input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

							<span class="input-group-btn">

								<button class="btn btn-default" type="submit">
									<span class="fa fa-search"></span>
								</button>

								<button class="btn btn-default" data-grid="generation" data-reset>
									<i class="fa fa-refresh fa-sm"></i>
								</button>

							</span>

						</div>

					</form>

				</div>

			</div>

		</nav>

	</header>

	<div class="panel-body">

		{{-- Grid: Applied Filters --}}
		<div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

			<div id="data-grid_applied" class="btn-group" data-grid="generation"></div>

		</div>

	</div>

	{{-- Grid: Table --}}
	<div class="table-responsive">

		<table id="data-grid" class="table table-hover" data-source="{{ route('admin.idmkr.adwords.generations.grid') }}" data-grid="generation">
			<thead>
				<tr>
					<th><input data-grid-checkbox="all" type="checkbox"></th>
					<th class="sortable" data-sort="id">{{{ trans('idmkr/adwords::generations/model.general.id') }}}</th>
					<th class="sortable" data-sort="adwords_batch_job_id">{{{ trans('idmkr/adwords::generations/model.general.adwords_batch_job_id') }}}</th>
					<th class="sortable" data-sort="templategroupeannonce_id">{{{ trans('idmkr/adwords::generations/model.general.templategroupeannonce_id') }}}</th>
					<th class="sortable" data-sort="feed_id">{{{ trans('idmkr/adwords::generations/model.general.feed_id') }}}</th>
					<th class="sortable" data-sort="adwords_feed_id">{{{ trans('idmkr/adwords::generations/model.general.adwords_feed_id') }}}</th>
					<th class="sortable" data-sort="operations_count">{{{ trans('idmkr/adwords::generations/model.general.operations_count') }}}</th>
					<th class="sortable" data-sort="status">{{{ trans('idmkr/adwords::generations/model.general.status') }}}</th>
					<th class="sortable" data-sort="ended_at">{{{ trans('idmkr/adwords::generations/model.general.ended_at') }}}</th>
					<th class="sortable" data-sort="adgroups_count">{{{ trans('idmkr/adwords::generations/model.general.adgroups_count') }}}</th>
					<th class="sortable" data-sort="spare_ads_count">{{{ trans('idmkr/adwords::generations/model.general.spare_ads_count') }}}</th>
					<th class="sortable" data-sort="customized_ads_count">{{{ trans('idmkr/adwords::generations/model.general.customized_ads_count') }}}</th>
					<th class="sortable" data-sort="keywords_count">{{{ trans('idmkr/adwords::generations/model.general.keywords_count') }}}</th>
					<th class="sortable" data-sort="feed_updates_count">{{{ trans('idmkr/adwords::generations/model.general.feed_updates_count') }}}</th>
					<th class="sortable" data-sort="enabled">{{{ trans('idmkr/adwords::generations/model.general.enabled') }}}</th>
					<th class="sortable" data-sort="created_at">{{{ trans('idmkr/adwords::generations/model.general.created_at') }}}</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

	</div>

	<footer class="panel-footer clearfix">

		{{-- Grid: Pagination --}}
		<div id="data-grid_pagination" data-grid="generation"></div>

	</footer>

	{{-- Grid: templates --}}
	@include('idmkr/adwords::generations/grid/index/results')
	@include('idmkr/adwords::generations/grid/index/pagination')
	@include('idmkr/adwords::generations/grid/index/filters')
	@include('idmkr/adwords::generations/grid/index/no_results')

</section>

@if (config('platform.app.help'))
	@include('idmkr/adwords::generations/help')
@endif

@stop
