<?php $user = get_user_by( 'id', $answer->user_id); ?>
<div class="copr-single-answer" id="copr-single-answer-<?php echo $answer->answer_id; ?>">
    <div class="copr-form-error"></div>
    <div class="copr-answer-details copr-flex-parent">
        <div class="copr-flex-child copr-avatar-wrapper">
            <a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo get_avatar($answer->user_id, 50); ?></a>
            <p><a href="<?php echo bp_core_get_user_domain($answer->user_id); ?>"><?php echo $user->display_name; ?></a></p>
        </div>
        <div class="copr-flex-child">
            <?php $additionalClass = (($canModerate) || (intval($answer->user_id) === intval($currentUserId))) ? ' copr-js-show' : ''; ?>
            <div class="copr-answer-text-wrapper<?php echo $additionalClass; ?>">
                <p class="copr-answer-text"><?php echo $answer->answer; ?></p>
                <p class="copr-date">
                    <span class="dashicons dashicons-calendar"></span>
                    <?php
                        if ($answer->updated_at !== '0000-00-00 00:00:00') {
                            $date = DateTime::createFromFormat('Y-m-d G:i:s', $answer->updated_at);
                        } else {
                            $date = DateTime::createFromFormat('Y-m-d G:i:s', $answer->created_at);
                        }
                        echo $date->format('M d, Y') . ' ' . __('at', 'copr-my-extension') . ' ' . $date->format('g:i A');
                    ?>
                </p>
            </div>
            <?php if (($canModerate) || (intval($answer->user_id) === intval($currentUserId))): ?>
                <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="copr-edit-answer copr-js-hide" data-error-message="<?php echo __( 'Sorry, we were unable to update the answer. Please try again later.', 'copr-my-extension' ); ?>">
                    <?php echo wp_nonce_field('edit_answer'); ?>
                    <input type="hidden" name="action" value="copr_update_answer_by_id" />
                    <input type="hidden" name="group_id" value="<?php echo $answer->group_id; ?>" />
                    <input type="hidden" name="answer_id" value="<?php echo $answer->answer_id; ?>" />
                    <?php if ($answer->question_type === 'text'): ?>
                        <textarea name="answer" class="copr-answer-textarea copr-question-textarea" rows="5"><?php echo $answer->answer; ?></textarea>
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
                        <input type="submit" value="Save" data-save="<?php echo __( 'Save', 'copr-my-extension' ); ?>" data-saving="<?php echo __( 'Saving', 'copr-my-extension' ); ?>">
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
            <form action="<?php echo admin_url('admin-ajax.php'); ?>" data-really-message="<?php echo __( 'Are you sure you want to delete it?', 'copr-my-extension' ); ?>" data-error-message="<?php echo __( 'Sorry, we were unable to delete the answer. Please try again later.', 'copr-my-extension' ); ?>" method="post">
                <?php echo wp_nonce_field('delete_answer'); ?>
                <input type="hidden" name="action" value="copr_delete_answer" />
                <input type="hidden" name="group_id" value="<?php echo $answer->group_id; ?>" />
                <input type="hidden" name="answer_id" value="<?php echo $answer->answer_id; ?>" />
                <input type="submit" class="copr-js-hide" value="Delete">
                <a href="#" class="copr-js-show copr-delete-answer"><span class="dashicons dashicons-trash"></span> <?php echo __( 'Delete', 'copr-my-extension' ); ?></a>
            </form>
        </div>
    <?php endif; ?>
</div>
