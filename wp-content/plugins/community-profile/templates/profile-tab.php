<div id="item-body">
    <h2 class="bp-screen-reader-text"><?php __( 'Community Profile', 'copr-my-extension' ) ?></h2>
    <div id="community-profile">
        <?php $currentTag = ''; ?>
        <?php $currentQuestion = ''; ?>

        <?php foreach ($answers as $answer): ?>
            <?php $user = get_user_by( 'id', $answer->user_id); ?>
            <?php
                if ($currentTag !== $answer->section_tag):
                    $currentTag = $answer->section_tag;
            ?>
                <h2 class="copr-section-title"><?php echo $answer->section_title; ?> (<?php echo strtoupper($answer->section_tag); ?>)</h2>
            <?php endif; ?>
            <?php
                if ($currentQuestion !== $answer->question_hash):
                    $currentQuestion = $answer->question_hash;
            ?>
                <h4 class="copr-question"><?php echo $answer->question; ?></h4>
            <?php endif; ?>
                <div class="copr-flex-parent copr-single-answer">
                    <div class="copr-flex-child copr-avatar-wrapper">
                        <a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo get_avatar($answer->user_id, 50); ?></a>
                        <p><a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo $user->display_name; ?></a></p>
                    </div>
                    <div class="copr-flex-child">
                        <p><?php echo $answer->answer; ?></p>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
</div>
