<?php

#namespace WilokeTest;

class PostMessage {
	const CHANNEL_ID = 'C027Q7QSTSB';
	const ENDPOINT   = 'https://hooks.slack.com/services/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';


	private static function buildBlock( $message, $aInfo ): array {
		if ( is_array( $aInfo ) && isset( $aInfo[0] ) && isset( $aInfo[0]['type'] ) ) {
			return $aInfo; // block already
		}

		$aMessage = [
			'type' => 'section',
			'text' => [
				'type' => 'mrkdwn',
				'text' => $message
			]
		];

		if ( ! empty( $imgUrl ) ) {
			$aMessage['accessory'] = [
				'type'      => 'image',
				'image_url' => $imgUrl,
				'alt_text'  => 'alt text for image'
			];
		}
		$aBlocks[] = $aMessage;

		$aBlocks[] = [
			'type' => 'section',
			'text' => [
				'type' => 'mrkdwn',
				'text' => is_array( $aInfo ) ? json_encode( $aInfo ) : $aInfo
			]
		];

		return $aBlocks;
	}

	public static function postMessage( $message, $aBlocks = [] ) {
		wp_remote_post(
			defined( 'SLACK_ENDPOINT' ) ? SLACK_ENDPOINT : self::ENDPOINT,
			[
				'headers'  => [
					'Content-Type' => 'application/json',
				],
				'blocking' => false,
				'body'     => json_encode( [
					'channel' => defined( 'SLACK_CHANNEL_ID' ) ? SLACK_CHANNEL_ID : self::CHANNEL_ID,
					'text'    => $message,
					'blocks'  => self::buildBlock( $message, $aBlocks )
				] )
			]
		);
	}
}
