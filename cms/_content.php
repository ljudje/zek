<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */


    include_once dirname(__FILE__) . '/' . 'components/utils/check_utils.php';
    CheckPHPVersion();
    CheckTemplatesCacheFolderIsExistsAndWritable();


    include_once dirname(__FILE__) . '/' . 'phpgen_settings.php';
    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    // OnGlobalBeforePageExecute event handler
    
    
    // OnBeforePageExecute event handler
    
    
    
    class _contentPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`_content`');
            $field = new IntegerField('id', null, null, true);
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('page_id');
            $this->dataset->AddField($field, false);
            $field = new StringField('position');
            $this->dataset->AddField($field, false);
            $field = new StringField('module_id');
            $this->dataset->AddField($field, false);
            $field = new StringField('title');
            $this->dataset->AddField($field, false);
            $field = new StringField('picture');
            $this->dataset->AddField($field, false);
            $field = new StringField('lead');
            $this->dataset->AddField($field, false);
            $field = new StringField('content');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('show_inquiry');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('hidden');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ord');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('is_system');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('page_id', '(SELECT
            			-- CONCAT(REPEAT(\'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\', (lvl - 1)), ttitle) AS title,
            			title_x,
            			-- title,
            			id
            		FROM
            		(
            			SELECT
            				t1.title AS lev1,
            				t2.title AS lev2,
            				t3.title AS lev3,
            				t4.title AS lev4,
            				t5.title AS lev5,
            				CONCAT_WS(\' > \',t1.title,t2.title,t3.title,t4.title,t5.title) AS title_x,
            				CASE WHEN t5.title IS NOT NULL THEN t5.id ELSE CASE WHEN t4.title IS NOT NULL THEN t4.id ELSE CASE WHEN t3.title IS NOT NULL THEN t3.id ELSE CASE WHEN t2.title IS NOT NULL THEN t2.id ELSE t1.id END END END END AS id,
            				CASE WHEN t5.title IS NOT NULL THEN t5.title ELSE CASE WHEN t4.title IS NOT NULL THEN t4.title ELSE CASE WHEN t3.title IS NOT NULL THEN t3.title ELSE CASE WHEN t2.title IS NOT NULL THEN t2.title ELSE t1.title END END END END AS ttitle,
            				CASE WHEN t1.id IS NOT NULL THEN 5 ELSE CASE WHEN t2.id IS NOT NULL THEN 4 ELSE CASE WHEN t3.id IS NOT NULL THEN 3 ELSE CASE WHEN t4.id IS NOT NULL THEN 2 ELSE 1 END END END END AS lvl
            			FROM
            				_page t1
            			RIGHT JOIN
            				_page t2
            				ON
            				t2.page_id = t1.id
            			RIGHT JOIN
            				_page t3
            				ON
            				t3.page_id = t2.id
            			RIGHT JOIN
            				_page t4
            				ON
            				t4.page_id = t3.id
            			RIGHT JOIN
            				_page t5
            				ON
            				t5.page_id = t4.id
            			ORDER BY
            				t1.title ASC,
            				t2.title ASC,
            				t3.title ASC,
            				t4.title ASC,
            				t5.title ASC
            		) AS tbl
            	ORDER BY title_x)', new IntegerField('id'), new StringField('title_x', 'page_id_title_x', 'page_id_title_x_pagesTree'), 'page_id_title_x_pagesTree');
            $this->dataset->AddLookupField('module_id', '_module', new StringField('id'), new StringField('title', 'module_id_title', 'module_id_title__module'), 'module_id_title__module');
            $this->dataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), '_content.is_system = 0'));
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(30);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        public function GetPageList()
        {
            $currentPageCaption = $this->GetShortCaption();
            $result = new PageList($this);
            $result->AddGroup($this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina'));
            $result->AddGroup($this->RenderText('<i class="kre3m kre3m-settings"></i>Nastavitve'));
            if (GetCurrentUserGrantForDataSource('_page')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Strani'), '_page.php', $this->RenderText('Strani'), $currentPageCaption == $this->RenderText('Strani'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            if (GetCurrentUserGrantForDataSource('_content')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Vsebina'), '_content.php', $this->RenderText('Vsebina'), $currentPageCaption == $this->RenderText('Vsebina'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            if (GetCurrentUserGrantForDataSource('_locale')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Nastavitve'), '_locale.php', $this->RenderText('Nastavitve'), $currentPageCaption == $this->RenderText('Nastavitve'), false, $this->RenderText('<i class="kre3m kre3m-settings"></i>Nastavitve')));
            if (GetCurrentUserGrantForDataSource('project')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Projekti'), 'project.php', $this->RenderText('Projekti'), $currentPageCaption == $this->RenderText('Projekti'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            
            if ( HasAdminPage() && GetApplication()->HasAdminGrantForCurrentUser() ) {
              $result->AddGroup('Admin area');
              $result->AddPage(new PageLink($this->GetLocalizerCaptions()->GetMessageString('AdminPage'), 'phpgen_admin.php', $this->GetLocalizerCaptions()->GetMessageString('AdminPage'), false, false, 'Admin area'));
            }
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function CreateGridSearchControl(Grid $grid)
        {
            $grid->UseFilter = true;
            $grid->SearchControl = new SimpleSearch('_contentssearch', $this->dataset,
                array('id', 'page_id_title_x', 'module_id_title', 'title', 'lead', 'content', 'hidden'),
                array($this->RenderText('Id'), $this->RenderText('Stran'), $this->RenderText('Naèin prikaza'), $this->RenderText('Naziv'), $this->RenderText('Uvod'), $this->RenderText('Vsebina'), $this->RenderText('Skrito')),
                array(
                    '=' => $this->GetLocalizerCaptions()->GetMessageString('equals'),
                    '<>' => $this->GetLocalizerCaptions()->GetMessageString('doesNotEquals'),
                    '<' => $this->GetLocalizerCaptions()->GetMessageString('isLessThan'),
                    '<=' => $this->GetLocalizerCaptions()->GetMessageString('isLessThanOrEqualsTo'),
                    '>' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThan'),
                    '>=' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThanOrEqualsTo'),
                    'ILIKE' => $this->GetLocalizerCaptions()->GetMessageString('Like'),
                    'STARTS' => $this->GetLocalizerCaptions()->GetMessageString('StartsWith'),
                    'ENDS' => $this->GetLocalizerCaptions()->GetMessageString('EndsWith'),
                    'CONTAINS' => $this->GetLocalizerCaptions()->GetMessageString('Contains')
                    ), $this->GetLocalizerCaptions(), $this, 'CONTAINS'
                );
        }
    
        protected function CreateGridAdvancedSearchControl(Grid $grid)
        {
            $this->AdvancedSearchControl = new AdvancedSearchControl('_contentasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('id', $this->RenderText('Id')));
            
            $selectQuery = 'SELECT
            			-- CONCAT(REPEAT(\'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\', (lvl - 1)), ttitle) AS title,
            			title_x,
            			-- title,
            			id
            		FROM
            		(
            			SELECT
            				t1.title AS lev1,
            				t2.title AS lev2,
            				t3.title AS lev3,
            				t4.title AS lev4,
            				t5.title AS lev5,
            				CONCAT_WS(\' > \',t1.title,t2.title,t3.title,t4.title,t5.title) AS title_x,
            				CASE WHEN t5.title IS NOT NULL THEN t5.id ELSE CASE WHEN t4.title IS NOT NULL THEN t4.id ELSE CASE WHEN t3.title IS NOT NULL THEN t3.id ELSE CASE WHEN t2.title IS NOT NULL THEN t2.id ELSE t1.id END END END END AS id,
            				CASE WHEN t5.title IS NOT NULL THEN t5.title ELSE CASE WHEN t4.title IS NOT NULL THEN t4.title ELSE CASE WHEN t3.title IS NOT NULL THEN t3.title ELSE CASE WHEN t2.title IS NOT NULL THEN t2.title ELSE t1.title END END END END AS ttitle,
            				CASE WHEN t1.id IS NOT NULL THEN 5 ELSE CASE WHEN t2.id IS NOT NULL THEN 4 ELSE CASE WHEN t3.id IS NOT NULL THEN 3 ELSE CASE WHEN t4.id IS NOT NULL THEN 2 ELSE 1 END END END END AS lvl
            			FROM
            				_page t1
            			RIGHT JOIN
            				_page t2
            				ON
            				t2.page_id = t1.id
            			RIGHT JOIN
            				_page t3
            				ON
            				t3.page_id = t2.id
            			RIGHT JOIN
            				_page t4
            				ON
            				t4.page_id = t3.id
            			RIGHT JOIN
            				_page t5
            				ON
            				t5.page_id = t4.id
            			ORDER BY
            				t1.title ASC,
            				t2.title ASC,
            				t3.title ASC,
            				t4.title ASC,
            				t5.title ASC
            		) AS tbl
            	ORDER BY title_x';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new MySqlIConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'pagesTree');
            $field = new StringField('title_x');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $lookupDataset->SetOrderBy('title_x', GetOrderTypeAsSQL(otAscending));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('page_id', $this->RenderText('Stran'), $lookupDataset, 'id', 'title_x', false, 8));
            
            $lookupDataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`_module`');
            $field = new StringField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('class');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('mode');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('is_system');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ord');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateLookupSearchInput('module_id', $this->RenderText('Naèin prikaza'), $lookupDataset, 'id', 'title', false, 8));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('title', $this->RenderText('Naziv')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('lead', $this->RenderText('Uvod')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('content', $this->RenderText('Vsebina')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('hidden', $this->RenderText('Skrito')));
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actionsBandName = 'actions';
            $grid->AddBandToBegin($actionsBandName, $this->GetLocalizerCaptions()->GetMessageString('Actions'), true);
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/view_action.png');
            }
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/edit_action.png');
                $column->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/delete_action.png');
                $column->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $column->SetAdditionalAttribute('data-modal-delete', 'true');
                $column->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
        }
    
        protected function AddFieldColumns(Grid $grid)
        {
            //
            // View column for title_x field
            //
            $column = new TextViewColumn('page_id_title_x', 'Stran', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('module_id_title', 'Naèin prikaza', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_contentGrid_title_handler_list');
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title_x field
            //
            $column = new TextViewColumn('page_id_title_x', 'Stran', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('module_id_title', 'Naèin prikaza', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_contentGrid_title_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for lead field
            //
            $column = new TextViewColumn('lead', 'Uvod', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_contentGrid_lead_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for content field
            //
            $column = new TextViewColumn('content', 'Vsebina', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_contentGrid_content_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for page_id field
            //
            $editor = new ComboBox('page_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT
            			-- CONCAT(REPEAT(\'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\', (lvl - 1)), ttitle) AS title,
            			title_x,
            			-- title,
            			id
            		FROM
            		(
            			SELECT
            				t1.title AS lev1,
            				t2.title AS lev2,
            				t3.title AS lev3,
            				t4.title AS lev4,
            				t5.title AS lev5,
            				CONCAT_WS(\' > \',t1.title,t2.title,t3.title,t4.title,t5.title) AS title_x,
            				CASE WHEN t5.title IS NOT NULL THEN t5.id ELSE CASE WHEN t4.title IS NOT NULL THEN t4.id ELSE CASE WHEN t3.title IS NOT NULL THEN t3.id ELSE CASE WHEN t2.title IS NOT NULL THEN t2.id ELSE t1.id END END END END AS id,
            				CASE WHEN t5.title IS NOT NULL THEN t5.title ELSE CASE WHEN t4.title IS NOT NULL THEN t4.title ELSE CASE WHEN t3.title IS NOT NULL THEN t3.title ELSE CASE WHEN t2.title IS NOT NULL THEN t2.title ELSE t1.title END END END END AS ttitle,
            				CASE WHEN t1.id IS NOT NULL THEN 5 ELSE CASE WHEN t2.id IS NOT NULL THEN 4 ELSE CASE WHEN t3.id IS NOT NULL THEN 3 ELSE CASE WHEN t4.id IS NOT NULL THEN 2 ELSE 1 END END END END AS lvl
            			FROM
            				_page t1
            			RIGHT JOIN
            				_page t2
            				ON
            				t2.page_id = t1.id
            			RIGHT JOIN
            				_page t3
            				ON
            				t3.page_id = t2.id
            			RIGHT JOIN
            				_page t4
            				ON
            				t4.page_id = t3.id
            			RIGHT JOIN
            				_page t5
            				ON
            				t5.page_id = t4.id
            			ORDER BY
            				t1.title ASC,
            				t2.title ASC,
            				t3.title ASC,
            				t4.title ASC,
            				t5.title ASC
            		) AS tbl
            	ORDER BY title_x';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new MySqlIConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'pagesTree');
            $field = new StringField('title_x');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $lookupDataset->SetOrderBy('title_x', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Stran', 
                'page_id', 
                $editor, 
                $this->dataset, 'id', 'title_x', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for module_id field
            //
            $editor = new ComboBox('module_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddMFUValue($this->RenderText('content'));
            $lookupDataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`_module`');
            $field = new StringField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('class');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('mode');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('is_system');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ord');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Naèin prikaza', 
                'module_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for title field
            //
            $editor = new TextEdit('title_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Naziv', 'title', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for lead field
            //
            $editor = new TextAreaEdit('lead_edit', 50, 8);
            $editColumn = new CustomEditColumn('Uvod', 'lead', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for content field
            //
            $editor = new HtmlWysiwygEditor('content_edit');
            $editor->SetAllowColorControls(true);
            $editColumn = new CustomEditColumn('Vsebina', 'content', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for hidden field
            //
            $editor = new CheckBox('hidden_edit');
            $editColumn = new CustomEditColumn('Skrito', 'hidden', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for page_id field
            //
            $editor = new ComboBox('page_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $selectQuery = 'SELECT
            			-- CONCAT(REPEAT(\'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\', (lvl - 1)), ttitle) AS title,
            			title_x,
            			-- title,
            			id
            		FROM
            		(
            			SELECT
            				t1.title AS lev1,
            				t2.title AS lev2,
            				t3.title AS lev3,
            				t4.title AS lev4,
            				t5.title AS lev5,
            				CONCAT_WS(\' > \',t1.title,t2.title,t3.title,t4.title,t5.title) AS title_x,
            				CASE WHEN t5.title IS NOT NULL THEN t5.id ELSE CASE WHEN t4.title IS NOT NULL THEN t4.id ELSE CASE WHEN t3.title IS NOT NULL THEN t3.id ELSE CASE WHEN t2.title IS NOT NULL THEN t2.id ELSE t1.id END END END END AS id,
            				CASE WHEN t5.title IS NOT NULL THEN t5.title ELSE CASE WHEN t4.title IS NOT NULL THEN t4.title ELSE CASE WHEN t3.title IS NOT NULL THEN t3.title ELSE CASE WHEN t2.title IS NOT NULL THEN t2.title ELSE t1.title END END END END AS ttitle,
            				CASE WHEN t1.id IS NOT NULL THEN 5 ELSE CASE WHEN t2.id IS NOT NULL THEN 4 ELSE CASE WHEN t3.id IS NOT NULL THEN 3 ELSE CASE WHEN t4.id IS NOT NULL THEN 2 ELSE 1 END END END END AS lvl
            			FROM
            				_page t1
            			RIGHT JOIN
            				_page t2
            				ON
            				t2.page_id = t1.id
            			RIGHT JOIN
            				_page t3
            				ON
            				t3.page_id = t2.id
            			RIGHT JOIN
            				_page t4
            				ON
            				t4.page_id = t3.id
            			RIGHT JOIN
            				_page t5
            				ON
            				t5.page_id = t4.id
            			ORDER BY
            				t1.title ASC,
            				t2.title ASC,
            				t3.title ASC,
            				t4.title ASC,
            				t5.title ASC
            		) AS tbl
            	ORDER BY title_x';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $lookupDataset = new QueryDataset(
              new MySqlIConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'pagesTree');
            $field = new StringField('title_x');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('id');
            $lookupDataset->AddField($field, true);
            $lookupDataset->SetOrderBy('title_x', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Stran', 
                'page_id', 
                $editor, 
                $this->dataset, 'id', 'title_x', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for module_id field
            //
            $editor = new ComboBox('module_id_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddMFUValue($this->RenderText('content'));
            $lookupDataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`_module`');
            $field = new StringField('id');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('class');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('mode');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('is_system');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ord');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->SetOrderBy('title', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Naèin prikaza', 
                'module_id', 
                $editor, 
                $this->dataset, 'id', 'title', $lookupDataset);
            $editColumn->SetAllowSetToDefault(true);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for title field
            //
            $editor = new TextEdit('title_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Naziv', 'title', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for lead field
            //
            $editor = new TextAreaEdit('lead_edit', 50, 8);
            $editColumn = new CustomEditColumn('Uvod', 'lead', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for content field
            //
            $editor = new HtmlWysiwygEditor('content_edit');
            $editor->SetAllowColorControls(true);
            $editColumn = new CustomEditColumn('Vsebina', 'content', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for hidden field
            //
            $editor = new CheckBox('hidden_edit');
            $editColumn = new CustomEditColumn('Skrito', 'hidden', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetAllowSetToDefault(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $grid->SetShowAddButton(true);
                $grid->SetShowInlineAddButton(false);
            }
            else
            {
                $grid->SetShowInlineAddButton(false);
                $grid->SetShowAddButton(false);
            }
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title_x field
            //
            $column = new TextViewColumn('page_id_title_x', 'Stran', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('module_id_title', 'Naèin prikaza', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for lead field
            //
            $column = new TextViewColumn('lead', 'Lead', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for content field
            //
            $column = new TextViewColumn('content', 'Content', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title_x field
            //
            $column = new TextViewColumn('page_id_title_x', 'Stran', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('module_id_title', 'Naèin prikaza', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for lead field
            //
            $column = new TextViewColumn('lead', 'Lead', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for content field
            //
            $column = new TextViewColumn('content', 'Content', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddExportColumn($column);
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        public function ShowEditButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasEditGrant($this->GetDataset());
        }
        public function ShowDeleteButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasDeleteGrant($this->GetDataset());
        }
        
        public function GetModalGridDeleteHandler() { return '_content_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, '_contentGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            
            $result->SetHighlightRowAtHover(true);
            $result->SetWidth('');
            $this->CreateGridSearchControl($result);
            $this->CreateGridAdvancedSearchControl($result);
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
            $this->SetShowPageList(true);
            $this->SetHidePageListByDefault(false);
            $this->SetExportToExcelAvailable(false);
            $this->SetExportToWordAvailable(false);
            $this->SetExportToXmlAvailable(false);
            $this->SetExportToCsvAvailable(false);
            $this->SetExportToPdfAvailable(false);
            $this->SetPrinterFriendlyAvailable(false);
            $this->SetSimpleSearchAvailable(true);
            $this->SetAdvancedSearchAvailable(false);
            $this->SetFilterRowAvailable(true);
            $this->SetVisualEffectsEnabled(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
    
            //
            // Http Handlers
            //
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_contentGrid_title_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_contentGrid_title_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for lead field
            //
            $column = new TextViewColumn('lead', 'Uvod', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_contentGrid_lead_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for content field
            //
            $column = new TextViewColumn('content', 'Vsebina', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_contentGrid_content_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            return $result;
        }
        
        public function OpenAdvancedSearchByDefault()
        {
            return false;
        }
    
        protected function DoGetGridHeader()
        {
            return '';
        }
    }

    SetUpUserAuthorization(GetApplication());

    try
    {
        $Page = new _contentPage("_content.php", "_content", GetCurrentUserGrantForDataSource("_content"), 'UTF-8');
        $Page->SetShortCaption('Vsebina');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Vsebina');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("_content"));
        GetApplication()->SetEnableLessRunTimeCompile(GetEnableLessFilesRunTimeCompilation());
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }
	
