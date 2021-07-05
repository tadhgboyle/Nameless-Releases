{include file='header.tpl'}

<body id="page-top">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        {include file='sidebar.tpl'}

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main content -->
            <div id="content">

                <!-- Topbar -->
                {include file='navbar.tpl'}

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Releases</h1>
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">Releases</li>
                        </ol>
                    </div>

                    <!-- Update Notification -->
                    {include file='includes/update.tpl'}

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <p style="margin-top: 7px; margin-bottom: 7px;">View all releases of NamelessMC.</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="float-md-right"><a href="{$NEW_LINK}" class="btn btn-primary"><i class="fas fa-plus-circle"></i> New Release</a></span>
                                </div>
                            </div>
                            {if isset($ALL_RELEASES)}
                                <hr />
                            {else}
                                <br />
                            {/if}

                            <!-- Success and Error Alerts -->
                            {include file='includes/alerts.tpl'}

                            {if isset($ALL_RELEASES)}
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Version Tag</th>
                                            <th>GitHub Release</th>
                                            <th>Required Version</th>
                                            <th>Urgent</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable">
                                        {foreach from=$ALL_RELEASES item=release}
                                        <tr>
                                            <td>{$release['name']}</td>
                                            <td><kbd>{$release['version_tag']}</kbd></td>
                                            <td><a href="{$release['github_link']}" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-fw fa-github"></i> Link</a></td>
                                            <td><kbd>{$release['required_version']}</kbd></td>
                                            <td>{if $release['urgent']} True {else} False {/if}</td>
                                            <td>{$release['created_at']}</td>
                                            <td>
                                                <a href="{$EDIT_LINK}{$release['id']}" class="btn btn-warning btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                            {else}
                                'No Releases'
                            {/if}
                        </div>
                    </div>

                    <!-- Spacing -->
                    <div style="height:1rem;"></div>

                    <!-- End Page Content -->
                </div>

                <!-- End Main Content -->
            </div>

            {include file='footer.tpl'}

            <!-- End Content Wrapper -->
        </div>

        <!-- End Wrapper -->
    </div>

    {include file='scripts.tpl'}

</body>

</html>