<div id="item-body">
    <h2 class="bp-screen-reader-text"><?php echo __( 'Community Profile', 'copr-my-extension' ); ?></h2>
    <div id="community-profile">
        <?php $currentTag = ''; ?>
        <?php $currentQuestion = ''; ?>
        <?php if (count($answers) === 0): ?>
            <p class="copr-no-answers"><?php echo __( 'Currently, there are no answers in this community\'s journey.', 'copr-my-extension' ); ?></p>
        <?php endif; ?>
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
                            <p class="copr-js-show copr-answer-text"><?php echo $answer->answer; ?></p>
                            <?php if (($canModerate) || (intval($answer->user_id) === intval($currentUserId))): ?>
                                <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="copr-edit-answer copr-js-hide">
                                    <?php echo wp_nonce_field('edit_answer'); ?>
                                    <input type="hidden" name="action" value="copr_update_answer_by_id" />
                                    <input type="hidden" name="group_id" value="<?php echo $groupId; ?>" />
                                    <input type="hidden" name="answer_id" value="<?php echo $answer->answer_id; ?>" />
                                    <?php if ($answer->question_type === 'text'): ?>
                                        <textarea name="answer" class="copr-answer-textarea" rows="10"><?php echo $answer->answer; ?></textarea>
                                    <?php elseif ($answer->question_type === 'choice'): ?>
                                        <?php $choices = explode(',', $answer->question_choices); ?>
                                        <div class="copr-answer-choices">
                                            <?php
                                                foreach ($choices as $choice) {
                                                    $checked = '';
                                					if (strtolower($choice) === strtolower($answer->answer)) {
                                						$checked = ' checked';
                                					}
                                					echo '<div><input type="radio" name="answer" value="' . $choice .'"' . $checked . ' /><label>' . $choice .'</label></div>';
                                                }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="submit copr-align-right">
                                        <input type="submit" value="Save">
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (($canModerate) || (intval($answer->user_id) === intval($currentUserId))): ?>
                        <div class="copr-answer-manage">
                            <a href="#" class="copr-js-show copr-edit-link" data-showing-text="<?php echo __( 'Hide', 'copr-my-extension' ); ?>" data-hiding-text="<?php echo __( 'Edit', 'copr-my-extension' ); ?>">
                                <span class="dashicons dashicons-edit"></span> <?php echo __( 'Edit', 'copr-my-extension' ); ?>
                            </a>
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
