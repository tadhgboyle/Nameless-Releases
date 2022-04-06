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
                                <h5 style="margin-top: 7px; margin-bottom: 7px;">{if $EDITING_RELEASE == null}Creating{else}Editing{/if} Release</h5>
                                </div>
                                <div class="col-md-3">
                                    <span class="float-md-right"><a href="{$BACK_LINK}" class="btn btn-primary">Back</a></span>
                                </div>
                            </div>
                            <hr />

                            <!-- Success and Error Alerts -->
                            {include file='includes/alerts.tpl'}

                            <form role="form" action="" method="post">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Full name of this release" value="{if ($EDITING_RELEASE)}{$EDITING_RELEASE->getName()}{/if}">
                                </div>
                                <div class="form-group">
                                    <label for="version_tag">Version Tag</label>
                                    <input type="text" name="version_tag" class="form-control" id="version_tag" placeholder="What to display in StaffCP as the NamelessMC version" value="{if ($EDITING_RELEASE)}{$EDITING_RELEASE->getVersionTag()}{/if}">
                                </div>
                                <div class="form-group">
                                    <label for="github_release_id">GitHub Release</label>
                                    <select class="form-control" name="github_release_id" id="github_release_id">
                                        {foreach from=$GITHUB_RELEASES item=release}
                                            <option value="{$release->id}" {if $EDITING_RELEASE && $EDITING_RELEASE->getGithubReleaseId() == $release->id} selected {/if}>{$release->name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="required_version">Required Version</label>
                                    <input type="text" name="required_version" class="form-control" id="required_version" placeholder="The version tag required to get this update" value="{if ($EDITING_RELEASE)}{$EDITING_RELEASE->getRequiredVersion()}{/if}">
                                </div>
                                <div class="form-group">
                                    <label for="urgent">Urgent</label>
                                    <input id="urgent" name="urgent" type="checkbox" {if $EDITING_RELEASE && $EDITING_RELEASE->isUrgent() eq 1}checked{/if} />
                                </div>
                                <div class="form-group">
                                    <label for="checksum">Checksum</label>
                                    <textarea name="checksum" id="checksum" class="form-control" rows="5">{if ($EDITING_RELEASE)}{$EDITING_RELEASE->getChecksum()}{/if}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="install_instructions">Install Instructions</label>
                                    <textarea style="width:100%" rows="10" name="install_instructions" id="install_instructions">{if $EDITING_RELEASE}{$EDITING_RELEASE->getInstallInstructions()}{/if}</textarea>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="token" value="{$TOKEN}">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                            </form>
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
