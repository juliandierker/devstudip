<? $launch_url  = $lti_link->getLaunchURL() ?>
<? $launch_data = $lti_link->getBasicLaunchData() ?>
<? $signature   = $lti_link->getLaunchSignature($launch_data) ?>
<iframe src="<?= vips_link('sheets/relay/iframe', compact('exercise_id', 'launch_url', 'launch_data', 'signature')) ?>" style="border: none; height: 640px; width: 100%;"></iframe>
