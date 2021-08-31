<div id="item-body">
    <h2 class="bp-screen-reader-text"><?php echo __( 'Community Profile', 'copr-my-extension' ); ?></h2>
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
                <div class="copr-single-answer" id="copr-single-answer-<?php echo $answer->answer_id; ?>">
                    <div class="copr-answer-details copr-flex-parent">
                        <div class="copr-flex-child copr-avatar-wrapper">
                            <a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo get_avatar($answer->user_id, 50); ?></a>
                            <p><a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo $user->display_name; ?></a></p>
                        </div>
                        <div class="copr-flex-child">
                            <p><?php echo $answer->answer; ?></p>
                        </div>
                    </div>
                    <?php if (($canModerate) || (intval($answer->user_id) === intval($currentUserId))): ?>
                        <div class="copr-answer-manage">
                            <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="copr-edit-answer" data-answer-id="<?php echo $answer->answer_id; ?>">
                                <?php echo wp_nonce_field('edit_answer'); ?>
                                <input type="submit" class="copr-js-hide" value="Edit">
                                <a href="#" class="copr-js-show"><span class="dashicons dashicons-edit"></span> <?php echo __( 'Edit', 'copr-my-extension' ); ?></a>
                            </form>
                            <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                                <?php echo wp_nonce_field('delete_answer'); ?>
                                <input type="hidden" name="action" value="copr_delete_answer" />
                                <input type="hidden" name="group_id" value="<?php echo $groupId; ?>" />
                                <input type="hidden" name="answer_id" value="<?php echo $answer->answer_id; ?>" />
                                <input type="submit" class="copr-js-hide" value="Delete">
                                <a href="#" class="copr-js-show copr-delete-answer"><span class="dashicons dashicons-trash"></span> <?php echo __( 'Delete', 'copr-my-extension' ); ?></a>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
        <?php endforeach; ?>
    </div>
</div>
