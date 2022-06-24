<div class="httemplates-templates-area">

    <!-- PopUp Content Start -->
    <div id="htwpt-popup-area" style="display: none;">
        <div class="httemplate-popupcontent">
            <div class='htwptspinner'></div>
            <div class="htwptmessage" style="display: none;">
                <p></p>
                <span class="htwpt-edit"></span>
            </div>
            <div class="htwptpopupcontent">
                <ul class="htwptemplata-requiredplugins"></ul>
                <p><?php esc_html_e( 'Import template to your Library', 'woolentor' ); ?></p>
                <span class="htwptimport-button-dynamic"></span>
                <div class="htpageimportarea">
                    <p> <?php esc_html_e( 'Create a new page from this template', 'woolentor' ); ?></p>
                    <input id="htwptpagetitle" type="text" name="htwptpagetitle" placeholder="<?php echo esc_attr_x( 'Enter a Page Name', 'placeholder', 'woolentor' ); ?>">
                    <span class="htwptimport-button-dynamic-page"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- PopUp Content End -->

    <div id="htwpt-search-section" class="htwpt-search-section section">
        <div class="container-fluid">
            <form action="#" class="htwpt-search-form">
                <div class="row">

                    <div class="col-md-auto col">
                        <div class="htwpt-demos-select">
                            <select id="htwpt-demos">
                                <option value="templates"><?php esc_html_e( 'Templates', 'woolentor' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-auto col">
                        <div class="htwpt-builder-select">
                            <select id="htwpt-builder">
                                <option value="all"><?php esc_html_e( 'All Builders', 'woolentor' ); ?></option>
                                <option value="elementor"><?php esc_html_e( 'Elementor', 'woolentor' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto mr-auto">
                        <input id="htwpt-search-field" type="text" placeholder="<?php esc_attr_e( 'Search..', 'woolentor' );?>">
                    </div>
                    <div class="col-auto">
                        <div class="htwpt-type-select">
                            <select id="htwpt-type">
                                <option value="all"><?php esc_html_e( 'ALL', 'woolentor' ); ?></option>
                                <option value="free"><?php esc_html_e( 'Free', 'woolentor' ); ?></option>
                                <option value="pro"><?php esc_html_e( 'Pro', 'woolentor' ); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="htwpt-project-section" class="htwpt-project-section section">
        <div id="htwpt-project-grid" class="htwpt-project-grid row" style="overflow: hidden;">
            <h2 class="htwpt-project-message"><span class="htwpt-pro-loading2"></span></h2>
        </div>
        <div id="htwpt-load-more-project" class="text-center"></div>
    </div>

    <div id="htwpt-group-section">
        <div id="htwpt-group-bar" class="htwpt-group-bar">
            <span id="htwpt-group-close" class="back"><i>&#8592;</i> <?php esc_html_e( 'Back to Library', 'woolentor' ); ?></span>
            <h3 id="htwpt-group-name" class="title"></h3>
        </div>

        <div id="htwpt-group-grid" class="row"></div>
        <a href="#top" class="htwpt-groupScrollToTop"><?php echo esc_html__( 'Top', 'woolentor' );?></a>
    </div>

    <a href="#top" class="htwpt-scrollToTop"><?php echo esc_html__( 'Top', 'woolentor' );?></a>

</div>