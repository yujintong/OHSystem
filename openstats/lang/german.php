<?php  
if ( !isset($website ) ) { header('HTTP/1.1 404 Not Found'); die; }  

/* 
  Translated by: ICanHasGrief 
  http://www.codelain.com/forum/index.php?action=profile;u=92608
*/

$lang = array();  
    
$lang["home"]       = "Home";  
$lang["top"]        = "Beste Spieler";  
$lang["game_archive"]  = "Spiele";  
$lang["media"]      = "Infos";  
$lang["guides"]     = "Tutorials";  
$lang["heroes"]     = "Helden";  
$lang["heroes_vote"]= "Helden Wahl";  
$lang["item"]       = "Gegenstand";  
$lang["items"]      = "Gegenstände";  
$lang["bans"]       = "Verbannte Spieler";  
$lang["all_bans"]   = "Alle gebannten Spieler";  
$lang["ban_report"] = "Ban beantragen";  
$lang["ban_appeal"] = "Unban beantragen";  
$lang["report_user"] = "Beschwerde einreichen";  
$lang["warn"]       = "Warnungen";  
$lang["warned"]     = "Verwarnt";  
$lang["expire"]     = "Auslauf"; 
$lang["expires"]    = "Expires"; 
$lang["expired"]    = "Expired";
$lang["search"]     = "Suche";  
$lang["search_players"] = "Suche Spieler...";  
$lang["admins"]     = "Administratoren";  
$lang["safelist"]   = "Gesicherte Spieler";  
$lang["about_us"]   = "Über uns";  
$lang["members"]    = "Mitglieder";  
$lang["live_games"]      = "Live Games";
    
$lang["username"]   = "Spieler";  

//ROLES
$lang["member_admin"]          = "Admin";
$lang["member_root"]           = "Root";
$lang["member_reserved"]       = "Vouched";
$lang["member_safe"]           = "Reserved Slot";
$lang["member_safe_reserved"]  = "Safelisted";
$lang["member_web_moderator"]  = "Website Moderator";
$lang["member_bot_moderator"]  = "Simple Bot Moderator";
$lang["member_bot_full_mod"]   = "Full Bot Moderator";
$lang["member_global_mod"]     = "Global Moderator";
$lang["member_bot_hoster"]     = "Hoster";

$lang["sort_by_role"]      = "Sort by role";
$lang["sort_by_bnet"]      = "Sort by bnet";
$lang["sort_by_default"]   = "Sort by default";
    
$lang["recent_games"]   = "Vergangene Spiele";  
$lang["recent_news"]    = "Vergangene News";  
    
$lang["profile"]         = "Profile";  
$lang["admin_panel"]     = "Admin Bereich";  
$lang["logout"]          = "Abmelden &times; ";  
$lang["login_register"]  = "Anmelden/Registrieren";  
$lang["login_fb_info"]   = "Klick auf den obrigen Button um sich via FB einzuloggen";  
$lang["total_comments"]  = "Kommentare";  
$lang["succes_registration"]  = "Du hast dich erfolgreich registriert!";  
    
$lang["profile_changed"]  = "Profil wurde erfolgreich aktualisiert!";  
$lang["password_changed"] = "Passwort wurde erfolgreich aktualisiert!";  

$lang["bnet_welcome_email_subject"] = "Registration successful - Bnet account";
$lang["bnet_welcome_email"] = "You have successfully registered your Battle.net account on our website.<br />Click the link below to verify your account.<br />";
$lang["bnet_mystats"]     = "View Stats";

$lang["unconfirmed_email"]     = "Unconfirmed email";
    
//APPEAL  
$lang["verify_appeal"]      = "Verefiziere den Spieler";  
$lang["verify_appeal_info"] = "Gib den Namen des Spielers ein um zu kontrollieren das er gebannt ist";  
$lang["appeal_here"]        = "Du kannst eine Aufhebung des Bannes hier beantragen: ";  
$lang["here"]               = "Klick mich";  
$lang["you_must_be"]        = "Du musst";  
$lang["logged_in"]          = "eingeloggt sein";  
$lang["to_appeal"]          = "um eine Aufhebung eines Bans zu beantragen. ";  
$lang["to_use_feature"]     = "to use this feature";
$lang["appeal_ban_date"]    = "Dieser Spieler wurde gebannt am";  
$lang["was_banned"]         = "Gebannt von";  
$lang["appeal_for"]         = "Aufhebung des Bannes für";  
$lang["subject"]            = "Typ";  
$lang["your_message"]       = "Deine Mitteilung";  
$lang["game_url"]           = "Spiel URL";  
$lang["replay_url"]         = "Replay URL";  

$lang["appeal_info1"] = "Your battle.net username:";
$lang["appeal_info2"] = "Here you can appeal if you are banned. To write an appeal, your account must be verified on battle net.";
$lang["appeal_info3"] = "Help?";
$lang["appeal_info4"] = "Go to";
$lang["appeal_info5"] = "your profile";
$lang["appeal_info6"] = "where you will see instructions for verifying profile.";
$lang["appeal_info7"] = "Verified accounts have battle.net icon next to the name:";
    
//REPORT  
$lang["report_player"]      = "Beschwerde einreichen über";  
$lang["report_reason"]      = "Grund der Beschwerde";  
$lang["report_submit"]      = "Beschwerde einreichen";  
$lang["error_report_player"]   = "Der Spieler Name ist zu kurz.";  
$lang["error_report_subject"]  = "Der Grund ist zu kurz.";  
$lang["error_report_reason"]   = "Der Text ist zu kurz.";  
$lang["error_no_player"]       = "Unsere Datenbank enthält keinerlei Informationen zu diesem Spieler";  
$lang["error_already_banned"]  = "Dieser Spieler ist bereits gebannt";   
$lang["error_report_login"]    = "Du musst eingeloggt sein um eine Beschwerde einzureichen,";  
$lang["error_report_time"]     = "Du kannst nicht so schnell hintereinander eine Beschwerden einreichen. Bitte warte einen Augenblick.";  
$lang["error_report_time2"]    = "Du kannst nicht so schnell hintereinander eine Aufhebung beantragen. Bitte warte einen Augenblick.";  
    
$lang["appeal_successfull"]    = "Aufhebung des Bans erfolgreich eingegangen. Bitte seien Sie geduldig bis wir Ihre Aufhebung bearbeitet haben."; 
$lang["report_successfull"]    = "Beschwerde über einen Spieler erfolgreich eingegangen. Bitte seien Sie geduldig bis wir Ihre Beschwerde bearbeitet haben.";  
    
//Time played. Ex. 10h 23m 16s  
$lang["h"]             = "h ";  
$lang["m"]             = "m ";  
$lang["s"]             = "s ";  
    
//Hero stats  
$lang["time_played"]             = "Spiel Zeit";  
$lang["average_loading"]         = "Durchschnittliche Lade Zeit";  
$lang["total_loading"]           = "Gesammte Ladezeit";  
$lang["seconds"]                 = "sek.";  
$lang["s"]                       = "s"; //seconds short  
$lang["favorite_hero"]           = "Favorisierter Held:";  
$lang["most_wins"]               = "Meisten gewonnen:";  
$lang["played"]                  = "gespielt";  
    
//Seconds  
$lang["error_sec"]             = "sek.";  
    
$lang["game"]       = "Spiel";  
$lang["duration"]   = "Zeit";  
$lang["type"]       = "Typ";  
$lang["date"]       = "Datum";  
$lang["map"]        = "Karte";  
$lang["creator"]    = "Ersteller";  
    
$lang["hero"]    = "Held";  
$lang["player"]  = "Spieler";  
$lang["kda"]     = "K/D/A";  
$lang["cdn"]     = "C/D/N";  
$lang["trc"]     = "T/R/C";  
$lang["gold"]    = "Gold";  
$lang["left"]    = "Verlassen";  
$lang["sent_winner"]    = "Sentinel hat gewonnen";  
$lang["scou_winner"]    = "Scourge hat gewonnen";  
$lang["sent_loser"]     = "Sentinel hat verloren";  
$lang["scou_loser"]     = "Scourge hat verloren";  
$lang["draw_game"]      = "Unentschiedenes Spiel";  
    
$lang["most_kills"]      = "Die meisten Kills:";  
$lang["most_assists"]    = "Die meisten Assists:";  
$lang["most_deaths"]     = "Die meisten Deaths:";  
$lang["top_ck"]          = "Die meisten Creep Kills:";  
$lang["top_cd"]          = "Die meisten Creep Denies:";  
    
$lang["score"]    = "Punkte";  
$lang["games"]    = "Spiele";  
$lang["wld"]     = "W/L/D";  
$lang["wl"]     = "W/L";  
$lang["tr"]     = "T/R";  
    
$lang["sortby"]     = "Sortieren nach:";  
$lang["wins"]       = "Gewonnen";  
$lang["losses"]     = "Verloren";  
$lang["draw"]       = "Unentschieden";  
    
$lang["kills"]       = "Kills";  
$lang["player_name"] = "Spielrname";  
$lang["deaths"]      = "Deaths";  
$lang["assists"]     = "Assists";  
$lang["ck"]          = "Creep Kills";  
$lang["cd"]          = "Creep Denies";  
$lang["nk"]          = "Neutral Kills";  
    
$lang["towers"]      = "Towers";  
$lang["rax"]         = "Rax";  
$lang["neutrals"]    = "Neutrals";  
$lang["submit"]          = "Abschicken";  
    
$lang["page"]          = "Seite";  
$lang["pageof"]        = "von";  
$lang["total"]         = "Insgesammt";  
$lang["next_page"]     = "Nächste Seite";  
$lang["previous_page"] = "Vorrige Seite";  
    
$lang["fastest_game"]   = 'Schnellstes Spiel gewonnen';  
$lang["longest_game"]   = 'Längstes Spiel gewonnen';  
    
$lang["game_history"]         = "Vergangene Spiele:";  
$lang["user_game_history"]    = "Vergangene Spiele des Spielers";  
$lang["best_player"]          = "Bester Spieler: ";  
$lang["show_hero_history"]    = "Zeige alle Spiele mit diesem Helden";
    
$lang["download_replay"]      = "Replay herunterladen";  
$lang["view_gamelog"]         = "Siehe Gamelog";  
    
$lang["win_percent"]          = "Win %";  
$lang["wl_percent"]           = "W/L%";  
$lang["kd_ratio"]             = "K/D Verhältniss";  
$lang["kd"]                   = "K/D";  
$lang["kpg"]                  = "KPG";  
$lang["kills_per_game"]       = "Kills pro Spiel";  
$lang["dpg"]                  = "DPG";  
$lang["apg"]                  = "APG";  
$lang["assists_per_game"]     = "Assists pro Spiel";  
$lang["ckpg"]                 = "CKPG";  
$lang["creeps_per_game"]      = "Creep kills pro Spiel";  
$lang["cdpg"]                 = "CDPK";  
$lang["denies_per_game"]      = "Denies pro Spiel";  
$lang["deaths_per_game"]      = "Deaths pro Spiel";  
$lang["npg"]                  = "NPG";  
$lang["neutrals_per_game"]    = "Neutrals pro Spiel";  
$lang["search_results"]       = "Spieler suche für: ";  
$lang["user_not_found"]       = "Spieler nicht gefunden";  
$lang["left_info"]            = "Wie oft ein Spieler vor dem Ende eines Spiels geleavt ist";  
    
$lang["admin"]       = "Administrator";  
$lang["server"]      = "Server";  
$lang["voucher"]     = "Erhalten von";  
$lang["role"]        = "Role";  

$lang["banned"]     = "VERBANNT";
$lang["permanent_ban"]   = "Permanent";
$lang["reason"]     = "Grund";  
$lang["game_name"]  = "Spiel Name";  
$lang["bannedby"]   = "Verbannt von";  
$lang["leaves"]     = "Verlassen";  
$lang["stayratio"]     = "Stay Verhältniss";  
$lang["leaver"]        = "Leaver";  
$lang["streak"]        = "Streak";  
$lang["longest_streak"]= "Längster Streak";  
$lang["losing_streak"] = "Longest Losing Streak"; 
$lang["zero_deaths"]   = "The total number of games where the player has 0 deaths"; 
    
$lang["comments"]             = "Kommentar";  
$lang["latest_comments"]      = "Latest Comments";
$lang["add_comment"]          = "Kommentar hinzufügen";  
$lang["add_comment_button"]   = "Kommentar hinzufügen";  
$lang["reply"]                = "[antworten]";  
    
$lang["error_comment_not_allowed"]   = "Kommentare hinzufügen ist in diesem Post nicht erlaubt";  
$lang["error_invalid_form"]          = "Falsche Form";  
$lang["error_text_char"]             = "Der Text ist zu kurz";  
    
$lang["gamestate_priv"]       = "PRIV";  
$lang["gamestate_pub"]        = "PUB";  
    
//Login / Registration  
$lang["login"]       = "Anmelden";  
$lang["logged_as"]   = "Angemeldet als ";  
$lang["email"]       = "E-mail";  
$lang["avatar"]      = "Avatar";  
$lang["location"]    = "Ort";  
$lang["realm"]       = "Realm";  
$lang["website"]     = "Homepage";  
$lang["gender"]      = "Geschlecht";  
$lang["male"]        = "Männlich";  
$lang["female"]      = "Weiblich";  
$lang["password"]    = "Passwort";  
$lang["register"]    = "Registrieren";  
$lang["username"]    = "Spielername";  
$lang["language"]    = "Sprache";
$lang["confirm_password"]       = "Passwort bestätigen";  
$lang["change_password"]        = "Passwort ändern";  
$lang["change_password_info"]   = "Klick auf die Box um dein Passwort zu ändern";  
$lang["comment_not_logged"]     = "Du musst eingeloggt sein um ein Kommentar zu verfassen";  
$lang["acc_activated"]          = "Account wurde erfolgreich aktiviert";  
$lang["invalid_link"]           = "Dieser Link existiert nicht oder ist schon ausgelaufen";  
    
//Heroes and items  
$lang["hero"]   = "Held";  
$lang["description"]     = "Beschreibung";  
$lang["stats"]           = "Stats";  
$lang["skills"]          = "Fähigkeiten";  
    
$lang["search"]          = "Suche";  
$lang["search_bans"]     = "Suche bans...";  
$lang["search_members"]  = "Suche Mitglieder...";  
$lang["search_heroes"]   = "Suche Helden...";  
$lang["search_items"]    = "Suche Gegenstände...";  
    
//Errors  
$lang["error_email"]      = "E-mail Adresse existiert nicht";  
$lang["error_email_banned"]      = "Email address is banned";
$lang["error_short_pw"]   = "Das Passwort ist zu kurz";  
$lang["error_passwords"]  = "Das Passwörter stimmen nihct überein";  
$lang["error_inactive_acc"]   = "Account wurde noch nicht aktiviert";  
$lang["error_invalid_login"]  = "Falsche E-Mail oder falsches Passwort";  
$lang["error_short_un"]   = "E-Mail oder Passwort ist zu kurz";  
$lang["error_un_taken"]   = "Der Username existiert bereits";  
$lang["error_username"]   = "Flascher Username";  
$lang["error_email_taken"]= "Die E-mail Adresse ust bereits vergeben";  
$lang["error_email_send"] = "E-mail has already been sent";
    
//Email  
$lang["email_charset"] = "UTF-8";  
$lang["email_subject_activation"] = "Account Aktivierung";  
$lang["email_from"] = "no-reply@ohsystem.net";
$lang["email_from_full"] = "OpenHosting";
$lang["email_send"] = "Email message was sent successfully";
    
//Email text  
$lang["email_activation1"] = "Hallo";  
$lang["email_activation2"] = "Du hast dich erfolgreich auf der StatsPage angemeldet ";  
$lang["email_activation3"] = "Klicke auf den folgenden Link um deinen Account zu verifizieren und zu bestätigen";  
    
//GAME LOG  
$lang["game_log"]            = "Spiel Log";  
$lang["log_player"]          = "Spieler";  
$lang["log_priv"]            = "[Priv]";  
$lang["log_ally"]            = "[Allies]";  
$lang["log_first_blood"]     = "für erstes Blut";  
$lang["log_suicide"]         = "hat sich selbst getötet!";  
$lang["log_denied_teammate"] = "killte seinen Mitspieler";  
$lang["log_level"]           = "Stufe";  
$lang["log_tower"]           = "Tower";  
$lang["log_barracks"]        = "Rax";  
    
$lang["404_error"]           = "Oops, Seite nicht gefunden";  
    
//VOTES  
$lang["vote_title"]          = "Wähle deinen Fevoriten";  
$lang["votes_won"]           = "Wahl Gewonnen";  
$lang["votes_lost"]          = "Wahl Verloren";  
$lang["votes_total"]         = "Alle Stimmen";  
$lang["votes_best"]          = "Bester";  
$lang["vote_vs"]             = "gegen";  
$lang["vote_sort"]           = "Abschicken";  
$lang["vote_results"]        = "Wahl Ergebnis";  
$lang["vote_back"]           = "Zurück zur Wahö";  
    
$lang["vote_won"]           = "Gewonnen:";  
$lang["vote_lost"]          = "Verloren:";  
$lang["vote_again"]         = "Nochmal wählen?";  
    
$lang["vote_error1"]        = "Klick bitte auf die Helden um zu wählen.";  
$lang["vote_display"]       = "Ergebnisse anzeigen";  
    
$lang["read_more"]          = "...[mehr]";  
    
$lang["upload_image"]      = "Bild hochladen";  
$lang["remove_avatar"]     = "Avatar entfernen";  
    
//Gamelist patch  
$lang["current_games"]      = "Aktuelle Spiele";  
$lang["refresh"]            = "Aktualisieren";  
$lang["slots"]              = "Slots";  
$lang["empty"]              = "Leer";  
$lang["ms"]                 = "ms";  
$lang["players"]            = "Players";
    
//Members  
$lang["joined"]         = "Beigetreten";  
$lang["user_info"]      = "Info";  
    
$lang["choose"]         = "Wähle";  
$lang["all_guides"]     = "---All guides---";  
    
//Compare players  
$lang["compare_back"]         = "&laquo; Zurück zu Top Spielern";  
$lang["compare_list"]         = "Liste";  
$lang["compare_list_empty"]   = "Liste ist leer";  
$lang["compare_compare"]      = "Vergleichen";  
$lang["compare_add"]          = "Zur Liste hinzufügen";  
$lang["compare_clear"]        = "Liste löschen";  
$lang["compare_remove_player"]   = "Entferne Spieler von der Liste?";  
$lang["compare_players"]      = "Spieler vergleichen";  
$lang["compare_empty_info"]   = "Die Liste der Spieler zum vergleichen ist leer";  
$lang["overall"]              = "Grafik:";  
$lang["stay"]              = "Stay";  

//Chat/GameLog
$lang["chat_attention"]     = "<b>Attention:</b> Do not abuse this feature or spam players, because this is not allowed and will be punished.";
$lang["chat_title_player"]  = "- send a message directly to the player in the game";
$lang["chat_successfull"]   = "<b>Message was successfully sent</b>";
$lang["chat_error1"]        = "Not enough characters";

$lang["gl_lobby"]    = "[Lobby]";
$lang["gl_system"]   = "[System]";
$lang["gl_bot"]      = "[BOT]";
$lang["gl_picked"]   = "picked up";
$lang["gl_swap"]     = "suggested to swap with";
$lang["gl_mode"]     = "Mode is set to:";
$lang["gl_heropick"] = "picked a hero";
$lang["gl_level"]    = "is now level";
$lang["gl_assists"]  = "Assists:";
$lang["gl_killed"]   = "killed";
$lang["gl_suicide"]  = "committed suicide.";
$lang["gl_used"]     = "used ";
$lang["gl_stored"]   = "stored ";
$lang["gl_rune"]     = "rune.";
$lang["gl_killed_t"] = "killed the";
$lang["gl_tower"]    = "tower.";
$lang["gl_rax"]      = "rax.";
$lang["gl_attack"]   = "attacks";
$lang["gl_killed_by"]= "was killed by the";
$lang["gl_item"]     = "bought";

$lang["gl_playerlist_sentinel_title"] = "Player (Level) - Sentinel";
$lang["gl_playerlist_scourge_title"] = "Player (Level) - Scourge";
$lang["gl_playerlist_kda"]        = "K/D/A";
$lang["gl_playerlist_send"]       = "Send a message directly to the player in the game";
$lang["gl_playerlist_sentinel"]       = "Sentinel";
$lang["gl_playerlist_scourge"]        = "Scourge";
$lang["gl_game_started"]              = "The game is started";

$lang["gl_compare_sentinel"] = "Player - Sentinel";
$lang["gl_compare_scourge"]  = "Player - Scourge";
$lang["gl_compare_wl"]       = "W/L";
$lang["gl_compare_perc"]     = "%";
$lang["gl_compare_wl_sentinel"]       = "Wins/Loses - SENTINEL";
$lang["gl_compare_sentinel_chance_t"]   = "Chance to win - SENTINEL";
$lang["gl_compare_sentinel_chance"]     = "Sentinel have a chance to win";

$lang["gl_compare_no"]     = "There are not enough players.";

$lang["gl_compare_wl_scourge"]         = "Wins/Loses - SCOURGE";
$lang["gl_compare_scourge_chance_t"]   = "Chance to win - SCOURGE";
$lang["gl_compare_scourge_chance"]     = "Scourge have a chance to win";

$lang["gl_calculate"]                  = "Calculate a chance to win";
$lang["gl_not_ranked"]                 = "not ranked yet";
$lang["gl_no_accesss"]                 = "You do not have access!";
$lang["gl_no_stats_data"]              = "Not enough statistical data";

$lang["gl_login"] = "Please <a href='".$website."?login'>login</a> to view the complete log";

$lang["member_comments"]     = "Comments";
$lang["member_registered"]   = "Registered";
$lang["member_location"]     = "Location";
$lang["member_realm"]        = "Realm";
$lang["member_website"]      = "Website";
$lang["member_gender"]       = "Gender";
$lang["member_last_login"]   = "Last login";
$lang["member_clan"]         = "Clan";

$lang["realm_username"]      = "Realm username";
$lang["protected_account"]   = "Protected Account";
$lang["protect_account"]     = "Protect Account";
$lang["acount_is_protected"] = "Your account is protected";
$lang["acount_is_not_protected"] = "Your account is not protected on Battle.net";
$lang["acount_protected_step"] = "One more step to protect your account";
$lang["set_password"]        = "Set Password";
$lang["protect_info1"]       = "You can protect your account with a password.";
$lang["protect_info2"]       = "<b>!pw</b> <i>[password]</i> - verify your account in game/lobby";
$lang["protect_info3"]       = "<b>!pw</b> <i>[password] clear</i> - remove protection";
$lang["protect_info4"]       = "More info";
$lang["protect_info5"]       = "In this way, your account can not be abused by someone else (use the same name as yours)";
$lang["protect_info6"]       = "Usage";
$lang["protect_info7"]       = "If you write the password in the field above <b>mypassword</b>, you can use the command";
$lang["protect_info8"]       = "<b>!pw mypassword</b> to protect your account";
$lang["protect_info9"]       = "<b>!pw mypassword clear</b> - remove protection or";
$lang["protect_info10"]      = "<b>!pw mypassword 0</b> - remove protection";
$lang["protect_info11"]      = "<b>Note:</b> If the password is set, and you <b>did not</b> use the command <b>!pw</b> you will be kicked within 30 seconds";
$lang["protect_info12"]      = "<i>Password can not contain blank spaces</i>";

$lang["protect_info13"]      = "You have entered the password, but your account is not yet verified on Battle.net";
$lang["protect_info14"]      = "To verify your account on Battle.net, use the command <b>!confirm email password</b>";
$lang["protect_info15"]      = "<b>Example:</b> Your email is <b>example@mail.com</b> and password is <b>password123</b>";
$lang["protect_info16"]      = "<b>Command:</b> !confirm example@mail.com password123";
$lang["protect_info17"]      = "Confirmed account has 2 icons next to the account name: ";

$lang["protect_info18"]      = "<i>This is not a password for your account on the website/Bnet, but a password to protect your account on Battle.net</i>";
$lang["protect_info19"]      = "<i>This password is only used for our hosting bots</i>";
$lang["protect_info20"]      = "<i>When you use the command <b>!pw</b>, other users can not see what you're typing</i>";
$lang["protect_info21"]      = "Show me the command";
$lang["protect_info22"]      = "After your account is verified, you can remove the password (if you want).";

//OFFENCES
$lang["player_offences"]     = "Player Offences";
$lang["player_warned"]       = "This player was warned";
$lang["has_total_of"]        = "times and has total of";
$lang["penalty_points"]      = "penalty points";
$lang["pp"]                  = "PP";

$lang["points"]              = "Points";

$lang["points_info1"]        = "You just got ";
$lang["points_info2"]        = "points";
$lang["points_info3"]        = "for daily login";
$lang["points_info4"]        = "Note: You must be ranked in order to get these points";
?>  