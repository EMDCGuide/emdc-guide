<div id="item-body">
    <h2 class="bp-screen-reader-text"><?php echo __( 'Community Profile', 'copr-my-extension' ); ?></h2>
    <div id="copr-community-profile">
        <div id="copr-section-filter" class="copr-flex-parent copr-js-show">
            <div class="copr-flex-child copr-print">
                <a href="#" onClick="window.print()"><span class="dashicons dashicons-printer"></span> <?php echo __( 'Print', 'copr-my-extension' ); ?></a>
            </div>
            <div class="copr-flex-child copr-filter">
                <span class="dashicons dashicons-filter"></span> <?php echo __( 'Filter By:', 'copr-my-extension' ); ?> <select name="copr-section-filter"><option value="all"><?php echo __( 'Display All', 'copr-my-extension' ); ?></option></select>
            </div>
        </div>
        <?php $currentTag = ''; ?>
        <?php $currentQuestion = ''; ?>
        <?php $userAnswered = false; ?>
        <?php if (count($answers) === 0): ?>
            <p class="copr-no-answers"><?php echo __( 'Currently, there are no answers in this community\'s journey.', 'copr-my-extension' ); ?></p>
        <?php endif; ?>
        <?php foreach ($answers as $key => $answer): ?>
            <?php
                if ($currentTag !== $answer->section_tag):
                    if ($currentTag !== '') {
                        echo '</div>';
                    }
                    $currentTag = $answer->section_tag;
                    $sectionTitle = $answer->section_title . ' (' . strtoupper($answer->section_tag) . ')';
            ?>
                <div id="section-<?php echo strtolower($answer->section_tag); ?>" class="copr-section-wrapper" data-title="<?php echo $sectionTitle; ?>" data-tag="<?php echo strtolower($answer->section_tag); ?>">
                    <h2 class="copr-section-title">
                        <?php
                            if ($answer->section_url !== '') {
                                echo '<a href="' . $answer->section_url . '" target="_blank">' . $sectionTitle . '</a>';
                            } else {
                                echo $sectionTitle;
                            }
                        ?>
                    </h2>
            <?php endif; ?>
            <?php
                if ($currentQuestion !== $answer->question_hash):
                    $currentQuestion = $answer->question_hash;
                    $userAnswered = false;
            ?>
                <h4 class="copr-question"><?php echo $answer->question; ?></h4>
            <?php endif; ?>
                <?php require('_single_answer.php'); ?>
                <?php
                    if (!$userAnswered) {
                        // Is this their answer?
                        $userAnswered = (intval($answer->user_id) === intval($currentUserId));
                    }
                    $nextKey = $key + 1;
                    if ((!array_key_exists($nextKey, $answers)) && (!$userAnswered)) {
                        // The last pass
                        // If the user did not answer the question then add a form to add an answer.
                        require('_my_response.php');
                    } else if (array_key_exists($nextKey, $answers)) {
                        $nextAnswer = $answers[$nextKey];
                        if (($currentQuestion !== $nextAnswer->question_hash) && (!$userAnswered)){
                            // If the user did not answer the question, and we are about to switch to a new question.  Add a form to add an answer.
                            require('_my_response.php');
                        }
                    }
                ?>
        <?php endforeach; ?>
    </div><!-- Last div for the final section title. -->
    </div>
</div>
<style media="print">
    /* Define print styles.  We do not want these throughout the site */
    .et_pb_row--with-menu > .et_pb_column_1_5,
    .lwp_divi_breadcrumbs,
    #sidebar,
    #item-header,
    #item-nav,
    #copr-section-filter,
    .et_section_transparent,
    .et_pb_section--fixed,
    .copr-answer-manage {
        display: none !important;
    }
    .entry-title .main_title {
        display: block;
    }
    .et-db #et-boc .et-l .et_pb_section_1_tb_footer.et_pb_section {
        background-color: #ffffff !important;
    }
    .et-db #et-boc .et-l .et_pb_section_1_tb_footer {
        border-color: #ffffff !important;
        border: 0px !important;
        box-shadow: none !important;
    }
</style>
