<?php
use com\cminds\downloadmanager\addon\clientdownloadzone\controller\AddonController;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\User;
?>
<div class="cmreg-list-users-invitations-shortcode">
    <?php if (!empty($myusers)): ?>
        <table>
            <thead><tr>
                <th data-col="id">ID</th>
                <th data-col="name">Name</th>
                <th data-col="email">Email</th>
                <th data-col="code">Code</th>
                <th data-col="code">Upload</th>
            </tr></thead>
            <tbody><?php foreach ($myusers as $user): ?>
                <?php
                $user_upload_link = AddonController::getDashboardUrl('add', array('for' =>
                    'client', 'user' => $user->user_nicename));
                ?>
                <tr>
                    <td data-col="id"><?php echo esc_html($user->data->ID); ?></td>
                    <td data-col="name">
                        <?php
                        $link = site_url('/') . get_option('CMDM_cmdownloads_slug') . "/user/" . $user->user_nicename;
                        echo '<a href="'.$link.'">'.esc_html($user->data->display_name).'</a>';
                        ?>
                    </td>
                    <td data-col="email"><?php echo esc_html($user->data->user_email); ?></td>
                    <td data-col="code"><?php echo get_user_meta($user->data->ID, 'cmreg_invitation_code_string', true); ?></td>
                    <td data-col="upload">
                        <a href="<?php echo $user_upload_link; ?>" class="upload"
                           title="Upload for this client"><span class="dashicons
                           dashicons-upload"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    <?php else: ?>
        <p>No user found.</p>
    <?php endif; ?>
</div>
