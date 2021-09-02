<div class="copr-my-response-wrapper">
    <p><?php echo __( 'Your Response', 'copr-my-extension' ); ?></p>
    <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="copr-my-response">
        <?php echo wp_nonce_field('submit_answers'); ?>
        <input type="hidden" name="action" value="copr_save_answer" />
        <input type="hidden" name="section_tag" value="<?php echo $answer->section_tag; ?>" />
        <input type="hidden" name="section_title" value="<?php echo $answer->section_title; ?>" />
        <input type="hidden" name="section_url" value="<?php echo $answer->section_url; ?>" />
        <input type="hidden" name="question_number" value="<?php echo $answer->question_number; ?>" />
        <input type="hidden" name="question_type" value="<?php echo $answer->question_type; ?>" />
        <input type="hidden" name="question_choices" value="<?php echo $answer->question_choices; ?>" />
        <input type="hidden" name="question" value="<?php echo $answer->question; ?>" />
        <input type="hidden" name="group_id" value="<?php echo $answer->group_id; ?>" />
        <div class="form-element-wrapper">
            <?php if ($answer->question_type === 'text'): ?>
                <textarea name="answer" class="copr-answer-textarea" rows="5"></textarea>
            <?php elseif ($answer->question_type === 'choice'): ?>
                <?php $choices = explode(',', $answer->question_choices); ?>
                <div class="copr-answer-choices">
                    <?php
                        foreach ($choices as $key => $choice) {
                            $checked = '';
                            if ($key === 0) {
                                $checked = ' checked';
                            }
                            echo '<div><input type="radio" name="answer" value="' . $choice .'"' . $checked . ' /><label>' . $choice .'</label></div>';
                        }
                    ?>
                </div>
            <?php endif; ?>
            <p class="copr-hidden copr-answer-error copr-error-message"></p>
        </div>
        <div class="submit copr-align-right">
            <input type="submit" value="Save" data-save="<?php echo __( 'Save', 'copr-my-extension' ); ?>" data-saving="<?php echo __( 'Saving', 'copr-my-extension' ); ?>">
        </div>
    </form>
</div>
