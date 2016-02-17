<?php /** @var \Dms\Web\Laravel\Renderer\Table\TableRenderer $tableRenderer */ ?>
<?php /** @var \Dms\Core\Common\Crud\Table\ISummaryTable $summaryTable */ ?>
<?php /** @var \Dms\Core\Module\ITableView[] $summaryTableViews */ ?>
@foreach($widgets as $widget)
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @foreach($summaryTableViews as $view)
                        <li class="{{ $view->isDefault() ? 'active' : '' }}"><a href="#summary-table-tab-{{ $view->getName() }}"
                                                                                data-toggle="tab">{{ $view->getLabel() }}</a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($summaryTableViews as $view)
                        <div class="tab-pane active" id="summary-table-tab-{{ $view->getName() }}">
                            {!! $tableRenderer->renderTableControl($packageName, $moduleName, $summaryTable, $view->getName()) !!}
                        </div>
                        <!-- /.tab-pane -->
                    @endforeach
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
@endforeach