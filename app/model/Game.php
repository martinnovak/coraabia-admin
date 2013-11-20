<?php

namespace Model;

use Framework;


class Game extends Model
{
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getCards()
	{
		return $this->getSource()->getSelectionFactory()->table('card');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getTranslations()
	{
		return $this->getSource()->getSelectionFactory()->table('translation');
	}
	
	
	/**
	 * @param array $deck
	 * @param array $cards
	 * @param array $connections
	 */
	public function createBotDeck(array $deck, array $cards, array $connections)
	{
		$deckId = 'DECK_' . $deck['deck_id'];
		
		$gameDeck = array(
			'deck_id' => $deckId,
			'start_tr' => '',
			'start_ch' => '',
			'type' => 'BOT_' . substr($deck['username'], 3)
		);
		$this->getSource()->query('INSERT INTO deck', $gameDeck);
		
		$cards = array_values(array_map(function ($item) use ($deckId) {
			return array('deck_id' => $deckId, 'card_id' => $item->card_id);
		}, $cards));
		if (count($cards)) {
			$this->getSource()->getSelectionFactory()->table('deck_card')
					->insert($cards);
		}
		
		$connections = array_values(array_map(function ($item) use ($deckId) {
			return array('connection_id' => $item->connection_id, 'deck_id' => $deckId);
		}, $connections));
		if (count($connections)) {
			$this->getSource()->getSelectionFactory()->table('deck_connection')
					->insert($connections);
		}
	}
	
	
	public function deleteBotDecks()
	{
		$this->getSource()->getSelectionFactory()->table('deck')
				->where('type ~ ?', '^BOT_[1-9][0-9]*$')
				->delete();
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getStaticTexts()
	{
		$keys = array(
			'BAZAAR_ALL',
			'CARD_MARK_BAZAAR',
			'CARD_MARK_DECK',
			'COLLECTION_FLAVOR_CHAPTER',
			'COLLECTION_FLAVOR_DUELWINS',
			'COLLECTION_FLAVOR_ERA',
			'COLLECTION_FLAVOR_HOME',
			'COLLECTION_FLAVOR_RACE',
			'COLLECTION_FLAVOR_SELL',
			'COLLECTION_FLAVOR_STORY',
			'CHAT_COMMANDS',
			'CHAT_HELP',
			'CHAT_LIST_OF_GAMEROOMS',
			'CHAT_PLAY',
			'CHAT_TRAVEL',
			'CHAT_WELCOME',
			'EDITOR_DECK_CARDS',
			'EDITOR_DECK_CHAMPS',
			'EDITOR_DECK_CHOOSE',
			'EDITOR_DECK_CONECTIONS',
			'EDITOR_DECK_DELETE',
			'EDITOR_DECK_DUPLICATE',
			'EDITOR_DECK_INFLUENCE',
			'EDITOR_DECK_LOAD',
			'EDITOR_DECK_NAME',
			'EDITOR_DECK_NEW',
			'EDITOR_DECK_RENAME',
			'EDITOR_DECK_SAVE',
			'EDITOR_DECK_SEARCH',
			'EDITOR_DECK_TRICKS',
			'DECK_CARDS',
			'DECK_CHAMPS',
			'DECK_CHOOSE',
			'DECK_CONECTIONS',
			'DECK_DELETE',
			'DECK_DUPLICATE',
			'DECK_INFLUENCE',
			'DECK_LOAD',
			'DECK_NAME',
			'DECK_NEW',
			'DECK_RENAME',
			'DECK_SAVE',
			'DECK_SEARCH',
			'DECK_TRICKS',
			'MAIN_MENU_BUTTON_BAZAAR',
			'MAIN_MENU_BUTTON_PLAY',
			'MAIN_MENU_BUTTON_COLLECTION',
			'MAIN_MENU_BUTTON_EDITOR',
			'MAIN_MENU_BUTTON_LOGOUT',
			'MAIN_MENU_BUTTON_PROFILE',
			'MAIN_MENU_BUTTON_SHOP',
			'MAIN_MENU_CHAT',
			'MAIN_MENU_SEED',
			'MAIN_MENU_BUTTON_DECK',
			'MAIN_MENU_MADE',
			'MAIN_MENU_BUTTON_XOTTERY',
			'DUEL_DECLARATION',
			'DUEL_WAITING',
			'DUEL_WIN',
			'DUEL_LOOSE',
			'DUEL_DRAW',
			'DUEL_GIVE_UP',
			'DUEL_PASS',
			'DUEL_REPLAY',
			'PROFILE_AVATARS',
			'PROFILE_NEWS',
			'PROFILE_OPTIONS',
			'PROFILE_STATS',
			'SHOP_IBLORT',
			'SHOP_XOTTERY',
			'DUEL_BERSERK_QUESTION',
			'DUEL_DESERTER_QUESTION',
			'DUEL_HAZARD_QUESTION',
			'DUEL_RESTART',
			'DUEL_CHAMPION_PLAYED',
			'DUEL_TRICK_PLAYED',
			'DUEL_ABILITY_PLAYED',
			'DUEL_ON_TURN',
			'DECK_INVALID',
			'GAME_POPUL_BUTTON',
			'GAME_ACTIVITY_BUTTON',
			'GAME_PLAY_BUTTON',
			'HELP_CARDS',
			'HELP_ABILITY',
			'RPG_HELP',
			'BUTTON_HELP',
			'COMMON_BUTTON_ALL',
			'PROFILE_OPTIONS_MUSIC',
			'PROFILE_OPTIONS_VOLUME',
			'PROFILE_OPTIONS_VOICE',
			'PROFILE_OPTIONS_AUTO',
			'PROFILE_OPTIONS_ANIM',
			'PROFILE_OPTIONS_NEWPASS',
			'PROFILE_OPTIONS_NEWPASS_CONF',
			'COMMON_BUTTON_CHANGE',
			'COMMON_GUARDIAN_SHORT',
			'COMMON_XENNO_SHORT',
			'COMMON_MERCENARY_SHORT',
			'COMMON_UNLIVING_SHORT',
			'COMMON_OUTLAW_SHORT',
			'COMMON_COMMON_SHORT',
			'COMMON_UNCOMMON_SHORT',
			'COMMON_GURU_SHORT',
			'COMMON_RARE_SHORT',
			'SUBNET_CONNECTION_LOST',
			'DM_CURRENT_DECK',
			'DM_INFULENCE_PROGRESS_BAR',
			'DM_DECK_CARDS_ALL',
			'DM_DECK_FRACTION_GUARDIAN',
			'DM_DECK_FRACTION_XENNO',
			'DM_DECK_FRACTION_MERCENARY',
			'DM_DECK_FRACTION_UNLIVING',
			'DM_DECK_FRACTION_OUTLAW',
			'DM_DECK_TRICKS',
			'DM_DECK_RARITY_COMMON',
			'DM_DECK_RARITY_UNCOMMON',
			'DM_DECK_RARITY_RARE',
			'DM_DECK_RARITY_GURU',
			'DM_VIEW_MINICARDS',
			'DM_VIEW_FULLCARDS',
			'DM_SELECT_FRACTION_GUARDIAN',
			'DM_SELECT_FRACTION_XENNO',
			'DM_SELECT_FRACTION_MERCENARY',
			'DM_SELECT_FRACTION_UNLIVING',
			'DM_SELECT_FRACTION_OUTLAW',
			'DM_SELECT_RARITY_COMMON',
			'DM_SELECT_RARITY_UNCOMMON',
			'DM_SELECT_RARITY_RARE',
			'DM_SELECT_RARITY_GURU',
			'DM_SELECT_HOLD_BUTTON',
			'DUEL_END_WIN',
			'DUEL_END_LOOSE',
			'DUEL_END_DRAW',
			'STATS_NAME',
			'STATS_WINS',
			'STATS_LOSES',
			'STATS_DRAWS',
			'STATS_SCORE',
			'STATS_LEVEL',
			'CONN_MULTIVERSE',
			'CONN_GAME',
			'PROFILE_STATS_PLAYED_GAMES_TOL',
			'PROFILE_STATS_PLAYED_GAMES_TU',
			'PROFILE_STATS_PLAYED_GAMES_TOTAL',
			'PROFILE_STATS_WINNING_TOL',
			'PROFILE_STATS_WINNING_TU',
			'PROFILE_STATS_WINNING_TOTAL',
			'PROFILE_STATS_DW_CARD',
			'PROFILE_STATS_POINTS_DUEL',
			'PROFILE_STATS_POINTS_GAME',
			'COLLECTION_FLAVOR_DW_LEFT',
			'HELP_GAME',
			'HELP_RPG',
			'PROFILENEWSLABEL',
			'PROFILENEWSTABLET',
			'DECKHAWARNING_FLAVOR_VIEW_END',
			'WARNING_DECK_SELECTION_NOTSELECTED',
			'WARNING_DECK_EMPTY_NAME',
			'WARNING_DECK_DELETE_LAST',
			'WARNING_DECK_DELETE_ACTUAL',
			'WARNING_DECK_INFLU_FINISHER',
			'WARNING_DECK_INFLU_TOPDECK',
			'WARNING_DECK_INFLU_CHAMP',
			'WARNING_DECK_INFLU_CONN',
			'WARNING_DECK_MAX_TRICK',
			'WARNING_DECK_MAX_CARD',
			'WARNING_DECK_INFLU_CARD',
			'WARNING_DECK_INFLU_TRICK',
			'WARNING_DECK_START_CHAMP_FULL',
			'WARNING_DECK_CONN_NOT_IN_DECK',
			'WARNING_DECK_ABSTRACTION_IN_DECK',
			'WARNING_SETTINGS_SAVE_FAILED',
			'WARNING_SETTINGS_PASS_NULL',
			'WARNING_SETTINGS_PASS_CHECK_NULL',
			'WARNING_SETTINGS_PASS_CHECK_FAILED',
			'WARNING_SETTINGS_PASS_CHANGED',
			'WARNING_SETTINGS_PASS_CHANGE_FAILED',
			'DECLARE_VIT',
			'DECLARE_INT',
			'DECLARE_KAR',
			'DECLARE_NEB',
			'PARAM_VI',
			'PARAM_IN',
			'PARAM_KA',
			'PARAM_NE',
			'BUTTON_CONFIRM_OK',
			'BUTTON_CONFIRM_YES',
			'BUTTON_CONFIRM_NO',
			'PROFILE_OPTIONS_OLDPASS',
			'WARNING_SETTINGS_OLD_PASS_NULL',
			'PLAYERX_REQUEST',
			'DM_HAND_CHAMP_BLANK',
			'DM_HAND_TRICK_BLANK',
			'DM_HAND_RANDOMCHAMP',
			'DM_HAND_RANDOMTRICK',
			'DM_HAND_CHAMP',
			'DM_HAND_TRICK',
			'DM_HAND_TOPDECK',
			'DM_HAND_FINISHER',
			'DM_CHARACTER_BLANK',
			'DM_TRICK_BLANK',
			'DM_CONNECTION_BLANK',
			'MAIN_MENU_CLOCK',
			'MAIN_MENU_SETTINGS',
			'MAIN_MENU_VOLUME',
			'PROFILE_NEWS_LABEL',
			'PROFILE_NEWS_TABLE_TOP',
			'GAME_REFRECH_PLAYERS',
			'MAIN_MENU_HELP',
			'PROFILE_STATS_WINS_TU',
			'PROFILE_STATS_WINS_TOL',
			'PROFILE_STATS_WINS_TOTAL',
			'DW_TABLE_TOTALDW',
			'DW_TABLE_DWGAIN',
			'DW_TABLE_CARDNAME',
			'DUEL_SKIP_TOOLTIP',
			'DUEL_RETREAT_TOOLTIP',
			'STATS_GAMES',
			'STATS_WINNING',
			'GAME_PLAYERS_ONLINE',
			'CANNOT_START_GAME',
			'LOGIN_INVALID',
			'DUEL_BONUS_ME',
			'DUEL_BONUS_OPP',
			'DUEL_SCORE_OPP',
			'DUEL_SCORE_ME',
			'MAIN_MENU_BUTTON_DECK_DISABLED',
			'MAIN_MENU_AVATAR',
			'GAME_ACTIVITY_REWARD',
			'GAME_ACTIVITY_TASK',
			'CHAT_GAME_BUTTON',
			'CHAT_GLOBAL_BUTTON',
			'COLLECTION_DW_LEFT',
			'GAME_NO_PLAY_BUTTON_TOOLTIP',
			'GAME_PLAY_BUTTON_TOOLTIP',
			'DM_SORT_BY_FRACT',
			'DM_SORT_BY_ALFA',
			'DM_SORT_BY_PRICE',
			'DM_CONN_INFLUENCE_DESC_DOWN',
			'DM_CONN_INFLUENCE_DESC_UP',
			'DM_SELECT_CONNECTIONS_GAME',
			'DM_SELECT_CONNECTIONS_MULTI',
			'DM_SELECT_CONNECTIONS_ALL',
			'DECK_CHARACTERS_MIN',
			'INFO_LEVEL_UP',
			'DUEL_REWARD_WINDOW',
			'DM_NEW_DECK_NAME',
			'DM_DECK_TRICK_BAR',
			'DM_DECK_CONNECTION_BAR',
			'DECK_INFLUENCE_MAX',
			'DM_HAND_CONNECTION_BLANK',
			'DM_HAND_TRICKS_BLANK',
			'GAME_LEVEL_UP_TEXT',
			'DECK_FRACTIONS',
			'DECK_SUBTYPE',
			'PROFILE_NEWS_TU_GURU_INFO',
			'PROFILE_OPTIONS_LANGUAGE',
			'DM_INFLUENCE_DIFER',
			'PROFILE_STATS_PLAYERS_CARD_TOOLTIP',
			'SHOP_ITEM_BUY_SUCCES',
			'SHOP_ITEM_BUY_FAILED',
			'MAIN_MENU_BUTTON_PLAY_INVALID_ICO_TOOLTIP',
			'MAIN_MENU_BUTTON_PLAY_VALID_ICO_TOOLTIP',
			'SHOP_ITEM_BUY_WINDOW_PRICE',
			'SHOP_ITEM_BUY_WINDOW_TITLE1',
			'SHOP_ITEM_BUY_WINDOW_TITLE',
			'DM_SORT_TRIN_TOOLTIP',
			'DM_SORT_XOT_TOOLTIP',
			'DM_SORT_ITEM_DOWN_TOOLTIP',
			'DM_SORT_ITEM_UP_TOOLTIP',
			'SHOP_BOOSTERY',
			'INFO_SETTINGS_LANG_SET',
			'SHOP_ITEM_LEFT',
			'SHOP_ITEM_BUY',
			'MAIN_MENU_XOT_TOLLTIP',
			'MAIN_MENU_TRIN_TOLLTIP',
			'EFFECT_AGRESSION_TOOLTIP',
			'EFFECT_PROHIBITION_TOOLTIP',
			'EFFECT_DOMINATION_TOOLTIP',
			'EFFECT_ISOLATION_TOOLTIP',
			'EFFECT_BERSERK_TOOLTIP',
			'EFFECT_SHACKLES_TOOLTIP',
			'EFFECT_CRITICAL_TOOLTIP',
			'EFFECT_AGGRESSION_NAME',
			'EFFECT_PROHIBITION_NAME',
			'EFFECT_DOMINATION_NAME',
			'EFFECT_ISOLATION_NAME',
			'EFFECT_BERSERK_NAME',
			'EFFECT_SHACKLES_NAME',
			'EFFECT_CRITICAL_NAME',
			'TOTAL_COUNT',
			'DM_INFLUENCE_DIFER',
			'PROFILE_STATS_PLAYERS_CARD_TOOLTIP',
			'GAME_GAMEROOM_CORAAB_TOOLTIP',
			'GAME_GAMEROOM_UNIVERSITY_TOOLTIP',
			'RESTART_NEB',
			'RESTART_INT',
			'RESTART_VIT',
			'RESTART_KAR',
			'DM_SORT_ITEM_TIME_TOOLTIP',
			'DM_FILTER_ITEM_CONNECTION',
			'DM_FILTER_ITEM_AVATAR',
			'DM_FILTER_ITEM_CARD',
			'DUEL_FORFEIT_QUESTION',
			'DUEL_END_ME_FORFEIT',
			'DUEL_END_OPP_FORFEIT',
			'DUEL_END_ME_TIMEOUT',
			'DUEL_END_OPP_TIMEOUT',
			'PROFILE_STATS_SCORE',
			'PROFILE_NEWS_TIMER_UN_TOOLTIP_HAZE',
			'PROFILE_NEWS_TIMER_UN_TOOLTIP_LIGHT',
			'STATS_POINTS',
			'PROFILE_NEWS_TIMER_TU_TOOLTIP',
			'PROFILE_NEWS_TIMER_TOL_TOOLTIP',
			'PROFILE_NEWS_TIMER_UN_TOOLTIP',
			'PROFILE_NEWS_TIMER_TOURNAMENT_LEFT',
			'PROFILE_NEWS_TIMER_PLR_ONLINE',
			'PROFILE_NEWS_TIMER_SESSION',
			'PROFILE_NEWS_TIMER_TOURNAMENT_LEFT_TOOLTIP',
			'PROFILE_NEWS_TIMER_PLR_ONLINE_TOOLTIP',
			'PROFILE_NEWS_TIMER_SESSION_TOOLTIP',
			'PROFILE_STATS_IBLORT',
			'PROFILE_NEWS_STATS_TOL',
			'PROFILE_NEWS_STATS_TU',
			'PROFILE_NEWS_STATS_UN',
			'PROFILE_STATS_DOWN',
			'PROFILE_STATS_UP',
			'PROFILE_STATS_EQUAL',
			'PROFILE_NEWS_SHOP_LABEL',
			'PROFILE_NEWS_TOPNEWS_LABEL',
			'COLLECTION_FLAVOR_DUELWINS_LEFT',
			'GAME_ACTIVITY_LEFT_TO_DONE',
			'GAME_ACTIVITY_MENU',
			'WARNING_DECK_NOTVALID_1',
			'WARNING_DECK_NOTVALID_2',
			'GAME_ACTIVITY_REWARD_CARD',
			'GAME_ACTIVITY_REWARD_CONNECTION',
			'PROFILE_NEWS_POLITICS_LABEL',
			'PROFILE_NEWS_POLITICS_TOOLTIP',
			'GAME_POLITICS_BUTTON',
			'PROFILE_OPTIONS_FILTERING',
			'PROFILE_NEWS_TIMER_SESSION_BER',
			'PROFILE_NEWS_TIMER_SESSION_NAME_TOOLTIP',
			'FRACTION_LABEL_OUTLAW',
			'FRACTION_LABEL_GUARDIAN',
			'FRACTION_LABEL_XENNO',
			'FRACTION_LABEL_MERCENARY',
			'FRACTION_LABEL_UNLIVING',
			'FRACTION_LABEL_UNSPECIFIED',
			'PLAYERS_CARD_TOOLTIP_BIRTHNUM',
			'STATS_STATE',
			'GAME_STATE_PLAYNG',
			'POLITICS_TOOLTIP',
			'SHOP_ITEM_TRIN_TOOLTIP',
			'SHOP_ITEM_XOT_TOOLTIP',
			'PROFILE_NEWS_PLAYERS_TU',
			'PROFILE_NEWS_PLAYERS_TOL',
			'PROFILE_NEWS_PLAYERS_UN',
			'PROFILE_NEWS_PLAYERS_LEFT',
			'PROFILE_NEWS_PLAYERS_RIGHT',
			'COMMON_BACK',
			'SHOP_XOTTERY_BUY',
			'SHOP_XOTTERY_BUY_TOOLTIP',
			'SHOP_XOTTERY_BASE_TITLE',
			'SHOP_XOTTERY_SUBTITLE_1',
			'SHOP_XOTTERY_SUBTITLE_2',
			'SHOP_XOTTERY_SUBTITLE_3',
			'SHOP_XOTTERY_ENTERCODE',
			'COMMON_CURRENCY',
			'SHOP_XOTTERY_VALUE_FOR_XOT_30',
			'SHOP_XOTTERY_VALUE_FOR_XOT_70',
			'SHOP_XOTTERY_VALUE_FOR_XOT_100',
			'SHOP_XOTTERY_VALUE_FOR_XOT_210',
			'SHOP_XOTTERY_VALUE_FOR_XOT_330',
			'SHOP_XOTTERY_VALUE_FOR_XOT_570',
			'SHOP_XOTTERY_VALUE_FOR_XOT_1200',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_1',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_2',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_3',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_4',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_5',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_6',
			'SHOP_XOTTERY_PRODUCT_TOOLTIP_7',
			'SHOP_XOTTERY_METHOD_SMS',
			'SHOP_XOTTERY_METHOD_PAYPAL',
			'SHOP_XOTTERY_METHOD_CARD',
			'SHOP_XOTTERY_METHOD_ACCOUNT',
			'SHOP_XOTTERY_METHOD_SMS_TOOLTIP',
			'SHOP_XOTTERY_METHOD_PAYPAL_TOOLTIP',
			'SHOP_XOTTERY_METHOD_CARD_TOOLTIP',
			'SHOP_XOTTERY_METHOD_ACCOUNT_TOOLTIP',
			'SHOP_XOTTERY_ENTERCODE_TOOLTIP',
			'SHOP_XOTTERY_CHECKBOX_RULES_TOOLTIP',
			'SHOP_XOTTERY_TITLE_RULES_TOOLTIP',
			'SHOP_XOTTERY_SMS_TITLE',
			'SHOP_XOTTERY_SMS_TEXT',
			'SHOP_XOTTERY_PAYPAL_TITLE',
			'SHOP_XOTTERY_PAYPAL_TEXT',
			'SHOP_XOTTERY_FINAL_TITLE',
			'SHOP_XOTTERY_FINAL_TEXT',
			'SHOP_XOTTERY_CARD_TITLE',
			'SHOP_XOTTERY_CARD_TEXT',
			'SHOP_XOTTERY_CARD_NAME',
			'SHOP_XOTTERY_CARD_AMOUNT',
			'SHOP_XOTTERY_CARD_CARD',
			'SHOP_XOTTERY_CARD_EXPIR',
			'SHOP_XOTTERY_CARD_ORDER',
			'SHOP_XOTTERY_CARD_ORDER_TOOLTIP',
			'SHOP_XOTTERY_BACK_TOOLTIP',
			'SHOP_XOTTERY_ACCOUNT_TITLE',
			'SHOP_XOTTERY_ACCOUNT_TEXT',
			'COMMON_VS',
			'COMMON_BANK',
			'COMMON_ACCOUNT',
			'SHOP_XOTTERY_ACCOUNT_AMOUNT',
			'SHOP_XOTTERY_ACCOUNT_ACCOUNT',
			'SHOP_XOTTERY_ACCOUNT_BANK',
			'SHOP_XOTTERY_ACCOUNT_VS',
			'SHOP_XOTTERY_BUY_READY_TOOLTIP',
			'SHOP_XOTTERY_ENTERCODE_EMPTY',
			'SHOP_XOTTERY_NOT_COMPLETE',
			'GAME_GAMEROOM_KHOPTID_TOOLTIP',
			'INPUT_SEARCH_TOOLTIP',
			'SHOP_GET_XOT_LABEL',
			'SHOP_GET_XOT_LABEL_TOOLTIP',
			'SHOP_BOOSTER_SHOW_ALL',
			'PLAYERS_CARD_TOLLTIP_AVATAR',
			'PLAYERS_CARD_TOLLTIP_BIRTHNUM',
			'PLAYERS_CARD_TOLLTIP_INFL',
			'SHOP_BOOST_CARD_COUNT',
			'SHOP_BOOST_PRICE_ONE',
			'SHOP_BOOST_BOOSTER',
			'SHOP_BOOST_EXPANSION',
			'SHOP_BOOST_CARD_ALL',
			'SHOP_BOOST_NEW_ABIL',
			'SHOP_BOOST_NEW_SUBTYPE',
			'SHOP_BOOST_NEW_CHARS',
			'SHOP_BOOST_TITLE_1',
			'SHOP_BOOST_INFO_1',
			'SHOP_BOOST_DESC_1',
			'SHOP_BOOST_CONTAIN_1',
			'SHOP_BOOST_CONTAIN_COUNT_1',
			'SHOP_BOOST_SUBTYPES_1',
			'SHOP_BOOST_CHARS_1',
			'SHOP_BOOST_TITLE_2',
			'SHOP_BOOST_INFO_2',
			'SHOP_BOOST_DESC_2',
			'SHOP_BOOST_CONTAIN_2',
			'SHOP_BOOST_CONTAIN_COUNT_2',
			'SHOP_BOOST_SUBTYPES_2',
			'SHOP_BOOST_CHARS_2',
			'SHOP_BOOST_TITLE_3',
			'SHOP_BOOST_INFO_3',
			'SHOP_BOOST_DESC_3',
			'SHOP_BOOST_CONTAIN_3',
			'SHOP_BOOST_CONTAIN_COUNT_3',
			'SHOP_BOOST_SUBTYPES_3',
			'SHOP_BOOST_CHARS_3',
			'SHOP_BOOST_TITLE_4',
			'SHOP_BOOST_INFO_4',
			'SHOP_BOOST_DESC_4',
			'SHOP_BOOST_CONTAIN_4',
			'SHOP_BOOST_CONTAIN_COUNT_4',
			'SHOP_BOOST_SUBTYPES_4',
			'SHOP_BOOST_CHARS_4',
			'SHOP_BOOST_BACK_TO_OFFER_TOOLTIP',
			'SHOP_BOOST_FIRE_CARD_TOOLTIP',
			'SHOP_BOOST_FIRE_ALLCARD_TOOLTIP',
			'SHOP_BOOST_REALLY_BUY',
			'SHOP_BOOST_DIALOG_PRICE',
			'SHOP_BOOST_DIALOG_COUNT',
			'PLAYERS_CARD_TOLLTIP_NE',
			'PLAYERS_CARD_TOLLTIP_IN',
			'PLAYERS_CARD_TOLLTIP_VI',
			'PLAYERS_CARD_TOLLTIP_KA',
			'DECK_NOT_VALID_4',
			'SELECT_AVATAR_ALL_TOOLTIP',
			'GAME_CORAAB_UNAVAILABLE_TOOLTIP',
			'GAME_UNIVERSITY_UNAVAILABLE_TOOLTIP',
			'GAME_KHOPTID_UNAVAILABLE_TOOLTIP',
			'DM_MIDDLE_BAR_TOOLTIP',
			'PROFILE_NEWS_BOOSTER_1',
			'PROFILE_NEWS_BOOSTER_2',
			'SHOP_XOTTERY_WARNING_CODE',
			'SHOP_XOTTERY_NOT_COMPLETE_1',
			'SHOP_XOTTERY_AGREEMENT',
			'SHOP_CONDITIONS',
			'SHOP_COMPLETE_RULES',
			'SHOP_PAYMENT_FINISHED',
			'BUTT_IBLORT_SELL_TOOLTIP',
			'SHOP_ITEM_SELL_SUCCES',
			'SHOP_ITEM_SELL_FAILED',
			'SHOP_ITEM_SELL_WINDOW_TITLE',
			'SHOP_ITEM_SELL_WINDOW_TITLE1',
			'DM_DUPLICATES_BUTTON_TOOLTIP',
			'GAME_PLAYERS_ONLINE_PLANET',
			'SHOP_IBLORT_IN_COLLECTION_TOOLTIP',
			'PROFILE_STATS_COLLECTION',
			'COMMON_ACCEPT',
			'COMMON_DECLINE',
			'COMMON_IGNORE',
			'GAME_PLAYER_CHALLENGE',
			'GAME_PLAYER_CHALLENGING',
			'GAME_REWARD_TEXT',
			'GAME_REWARD',
			'COMMON_CONGRATS',
			'CHAT_BUTTON_CHALLENGE_TOOLTIP',
			'CHALLENGE_IGNORE_TOOLTIP',
			'CORAAB_TIME',
			'COLLECTION_MULTIPLE_CARDS_TOOLTIP',
			'CHAT_PLNETAR_CHANNEL',
			'CHAT_USER_CHANNEL',
			'CHAT_GAME_CHANNEL',
			'CHAT_PLAYER_CHANNEL_TOOLTIP',
			'DUEL_FFRIEND_DUEL_STARTING',
			'DUEL_FFRIEND_DUEL_FINISHED',
			'COMMON_FRACT_TRICS_TOOLTIP',
			'PROFILE_NEWS_TABLE_BUTTON_WINNING',
			'PROFILE_NEWS_TABLE_BUTTON_GAMES',
			'PROFILE_NEWS_TABLE_BUTTON_TOURNAMENT',
			'PROFILE_NEWS_TABLE_BUTTON_LOTERY',
			'PROFILE_NEWS_TABLE_BUTTON_WINNING_TOOLTIP',
			'PROFILE_NEWS_TABLE_BUTTON_GAMES_TOOLTIP',
			'PROFILE_NEWS_TABLE_BUTTON_TOURNAMENT_TOOLTIP',
			'PROFILE_NEWS_TABLE_BUTTON_LOTERY_TOOLTIP',
			'PROFILE_STATS_COLLECTION_TOOLTIP',
			'PROFILE_STATS_WINS_TOTAL_TOOLTIP',
			'PROFILE_STATS_PLAYED_GAMES_TOTAL_TOOLTIP',
			'COLLECTION_FLAVOR_TRICK',
			'GAME_TOURNAMENT_ENDS',
			'GAME_TOURNAMENT_PROCESS_TOOLTIP',
			'GAME_TOURNAMENT_ENDS_TOOLTIP',
			'GAME_TOURNAMENT_STARTS_TOOLTIP',
			'GAME_TOURNAMENT_INFLU',
			'GAME_TOURNAMENT_FINISH_REWARD',
			'PROFILE_NEWS_TIMER_TOURNAMENT_ENDS_TOOLTIP',
			'GAME_TOURNAMENT_STARTING',
			'DUEL_HANDSHAKE_FAILED',
			'DUEL_HANDSHAKE_WAITING',
			'UNAMENT_INFLUENCE_INVALID',
			'UNAMENT_ALREADY_FINISHED',
			'PROFILE_NEWS_CONSOLE_BOTTM_TOOLTIP',
			'DM_FILTER_OWNED_ITEMS_TOOLTIP',
			'PROFILE_NEWS_KHOPT_LIGA',
			'STATS_KHOPTS',
			'GAME_TOURNAMENT_COLLECTOR',
			'GAME_TOURNAMENT_POSITION',
			'GAME_TOURNAMENT_PROGRESS',
			'SUBNET_CONNECTION_CLOSED_SECOND_SESSION',
			'GAME_TOURNAMENT_SCORE_POINT',
			'GAME_TOURNAMENT_SCORE_POINTS_234',
			'GAME_TOURNAMENT_SCORE_POINTS_5',
			'PROFILE_NEWS_PLAYERS_UN_2',
			'PROFILE_NEWS_TABLE_BUTTON_ELO',
			'PROFILE_NEWS_TABLE_BUTTON_ELO_TOOLTIP',
			'STATS_ELO',
			'COLLECTION_FLAVOR_BUY',
			'COLLECTION_FLAVOR_FLAVOR',
			'PROFILE_NEWS_ELO',
			'DECK_PROMPT_DELETE_DECK',
			'UNAMENT_NO_CURRENT_UNAMENT',
			'UNAMENT_FILTER_FAIL',
			'Unament_I',
			'Unament_II',
			'Unament_III',
			'Unament_IV',
			'Unament_V',
			'Unament_VI',
			'Unament_VII',
			'Unament_VIII',
			'Tolnament_I',
			'Tolnament_II',
			'Tolnament_III',
			'Tolnament_IV',
			'Tolnament_V',
			'Tolnament_VI',
			'Tolnament_VII',
			'Tolnament_VIII',
			'Tunament_I',
			'Tunament_II',
			'Tunament_III',
			'Tunament_IV',
			'Tunament_V',
			'Tunament_VI',
			'Tunament_VII',
			'Tunament_VIII',
			'PLAYER_CARD_FRACTION_GUARDIAN',
			'PLAYER_CARD_FRACTION_OUTLAW',
			'PLAYER_CARD_FRACTION_XENNO',
			'PLAYER_CARD_FRACTION_MERCENARY',
			'PLAYER_CARD_FRACTION_UNLIVING',
			'PLAYER_CARD_FRACTION_GENERAL',
			'PLAYER_CARD_NAME',
			'PLAYER_CARD_LVL',
			'PLAYER_CARD_BANNER_KHOPT',
			'PLAYER_CARD_BANNER_ELO'
		);
		return $this->getSource()->getSelectionFactory()->table('translation')
				->where('key IN ?', $keys)
				->where('lang = ?', $this->locales->lang);
	}
	
	
	/**
	 * @param string $key
	 * @param string $lang
	 * @param string $value
	 */
	public function updateStaticText($key, $lang, $value)
	{
		$this->getSource()->getSelectionFactory()->table('translation')
				->where('key = ?', $key)
				->where('lang = ?', $lang)
				->fetch()
				->update(array('value' => $value));
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getEditions()
	{
		return $this->getSource()->getSelectionFactory()->table('edition');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getArtists()
	{
		return $this->getSource()->getSelectionFactory()->table('artist')
				->select('artist.*, COUNT(:art.art_id) AS arts')
				->group('artist.artist_id');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getCountries()
	{
		return $this->getSource()->getSelectionFactory()->table('translation')
				->where('key LIKE ?', 'country.%')
				->where('lang = ?', $this->locales->lang);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getArts()
	{
		return $this->getSource()->getSelectionFactory()->table('art')
				->select('art.*, image.path AS art_path, face.path AS face_path, avatar.path AS avatar_path');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivities()
	{
		return $this->getSource()->getSelectionFactory()->table('activity');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getFilters()
	{
		return $this->getSource()->getSelectionFactory()->table('filter');
	}
	
	
	/**
	 * @return array
	 */
	public function getFractions()
	{
		return array(
			'GUARDIAN',
			'XENNO',
			'MERCENARY',
			'UNLIVING',
			'OUTLAW'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityVariants()
	{
		return array(
			'ACTIVITY',
			'TITLE',
			'GRIND'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityTypes()
	{
		return array(
			'CLASSIC',
			'SPECIAL',
			'TOURNAMENT'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityStartTypes()
	{
		return array(
			'MM',
			'PVP',
			'ELO'
		);
	}
		
	
	/**
	 * @param string $table
	 * @param mixed $activityId
	 * @param array $values
	 * @return \Nette\Database\Table\ActiveRow|NULL
	 */
	public function update($table, $id, array $values)
	{
		if ($id !== NULL) { //update
			$this->getSource()->getSelectionFactory()->table($table)
					->where($table . '_id = ?', $id)
					->fetch()
					->update($values);
		} else { //insert
			return $this->getSource()->getSelectionFactory()->table($table)
					->insert($values);
		}
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getGamerooms()
	{
		return $this->getSource()->getSelectionFactory()->table('gameroom');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getObservers()
	{
		return $this->getSource()->getSelectionFactory()->table('observer');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityGamerooms()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_gameroom');
	}
	
	
	/**
	 * @todo optimize
	 * @param string $activityId
	 * @return \Nette\Database\Table\Selection
	 */
	public function getParentActivities($activityId)
	{
		$obj = Framework\Kapafaa\ObjectFactory::getActivityPlayableSetter($activityId);
		
		//All observers that set my playable variable to TRUE.
		$observers = $this->getObservers()
				->select('observer_id')
				->where('effect_data LIKE ?', "%$obj%");
		return $this->getActivities()
				->where(':activity_observer.observer_id IN ?', $observers);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getBots()
	{
		return $this->getSource()->getSelectionFactory()->table('bot');
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityRewardTypes()
	{
		return array(
			'CONNECTION',
			'TRIN',
			'CARD',
			'EXP',
			'XOT',
			'AVATAR'
		);
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\ResultSet|NULL
	 */
	public function createActivity(array $values)
	{
		return $this->getSource()->query('INSERT INTO activity', array(
						'activity_id' => $values['activity_id'],
						'fraction' => $values['fraction'] ?: NULL,
						'posx' => (int)$values['posx'],
						'posy' => (int)$values['posy'],
						'authority' => $values['authority'],
						'art_id' => $values['art_id'] ? (int)$values['art_id'] : NULL,
						'bot_id' => $values['bot_id'] ? (int)$values['bot_id'] : NULL,
						'variant_type' => $values['variant_type'],
						'activity_type' => $values['activity_type'],
						'start_type' => $values['start_type'],
						'reward_type' => $values['reward_type'] ?: NULL,
						'reward_value' => $values['reward_type'] && $values['reward_value'] ? $values['reward_value'] : NULL,
						'tree' => (int)$values['tree'],
						'ready' => FALSE
					));
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\Table\Selection
	 */
	public function createFilter(array $values)
	{
		return $this->getSource()->getSelectionFactory()->table('filter')
				->insert($values);
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\Table\Selection
	 */
	public function createObserver(array $values)
	{
		return $this->getSource()->getSelectionFactory()->table('observer')
				->insert($values);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityObservers()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_observer');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityPlayableFilters()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_filter_playable');
	}
	
	
	/**
	 * @return array
	 */
	public function getConnections()
	{
		$result = array();
		foreach ($this->getSource()->query(
				'SELECT
					connection.*
				FROM connection
				LEFT JOIN connection_version USING (connection_id, version)
				WHERE
					connection_version.server = ?',
				$this->locales->server)
				->fetchAll() as $connection) {
			$result[$connection->connection_id] = $connection;
		}
		return $result;
	}
	
	
	/**
	 * @param string $connectionId
	 * @param int $version
	 */
	public function deleteConnection($connectionId, $version)
	{
		try {
			$this->getSource()->beginTransaction();
			
			$this->getSource()->query(
					'DELETE FROM connection
					WHERE
						connection_id = ? AND
						version = ?',
					$connectionId, $version);
			
			$this->getSource()->commit();
		} catch (\Exception $e) {
			$this->getSource()->rollBack();
			throw $e;
		}
	}
}
