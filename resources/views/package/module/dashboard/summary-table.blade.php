<?php /** @var \Dms\Web\Laravel\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Laravel\Renderer\Table\TableRenderer $tableRenderer */ ?>
<?php /** @var \Dms\Core\Module\IModule $module */ ?>
<?php /** @var \Dms\Core\Module\IAction $generalActions */ ?>
<?php /** @var \Dms\Core\Common\Crud\Table\ISummaryTable $summaryTable */ ?>
<?php /** @var \Dms\Core\Module\ITableView[] $summaryTableViews */ ?>
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom dms-table-tabs">
            <ul class="nav nav-tabs">
                @foreach($summaryTableViews as $view)
                    <li class="{{ $activeViewName === $view->getName() ? 'active' : '' }}">
                        <a class="dms-table-tab-show-button" href="#summary-table-table-{{ $view->getName() }}" data-toggle="tab">{{ $view->getLabel() }}</a>
                    </li>
                @endforeach
                @if($createActionName ?? false)
                    <li class="pull-right dms-general-action-container">
                        <button class="btn btn-success" data-a-href="{{ $moduleContext->getUrl('action.form', [$createActionName]) }}">
                            Add <i class="fa fa-plus-circle"></i>
                        </button>
                    </li>
                @endif
                @foreach(array_reverse($generalActions) as $action)
                    <li class="pull-right dms-general-action-container">
                        @if($action instanceof \Dms\Core\Module\IUnparameterizedAction)
                            <button class="dms-run-action-form inline btn btn-{{ \Dms\Web\Laravel\Util\KeywordTypeIdentifier::getClass($action->getName()) }}"
                                 data-action="{{ $moduleContext->getUrl('action.run', [$action->getName()]) }}"
                                 data-method="post"
                            >
                                {{ \Dms\Web\Laravel\Util\StringHumanizer::title($action->getName())  }}
                            </button>
                        @else
                            <a  class="btn btn-{{ \Dms\Web\Laravel\Util\KeywordTypeIdentifier::getClass($action->getName()) }}"
                                href="{{ $moduleContext->getUrl('action.form', [$action->getName()]) }}"
                            >{{ \Dms\Web\Laravel\Util\StringHumanizer::title($action->getName()) }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($summaryTableViews as $view)
                    <div class="tab-pane {{ $view->isDefault() ? 'active' : '' }}" id="summary-table-table-{{ $view->getName() }}">
                        {!! $tableRenderer->renderTableControl($moduleContext, $summaryTable, $view->getName()) !!}
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