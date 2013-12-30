<?php
if ( !isset($website ) ) { header('HTTP/1.1 404 Not Found'); die; }

$lang = array();

$lang["home"]       = "Home";
$lang["top"]        = "Top Players";
$lang["game_archive"]  = "Games";
$lang["media"]      = "Misc";
$lang["guides"]     = "Guides";
$lang["heroes"]     = "Heroes";
$lang["heroes_vote"]= "Heroes Vote";
$lang["item"]       = "Item";
$lang["items"]      = "Items";
$lang["bans"]       = "Bans";
$lang["all_bans"]   = "All Bans";
$lang["ban_report"] = "Ban Report";
$lang["ban_appeal"] = "Ban Appeal";
$lang["report_user"] = "Report user";
$lang["warn"]       = "Warns";
$lang["warned"]     = "Warned";
$lang["expire"]     = "Expire";
$lang["expires"]    = "Expires";
$lang["expired"]    = "Expired";
$lang["search"]     = "Search";
$lang["search_players"] = "Search players...";
$lang["admins"]     = "Admins";
$lang["safelist"]   = "Safelist";
$lang["about_us"]   = "About Us";
$lang["members"]    = "Members";
$lang["live_games"]      = "Live Games";


$lang["username"]   = "Username";

//ROLES
$lang["member_admin"]          = "Admin";
$lang["member_root"]           = "root";
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

$lang["recent_games"]   = "Recent Games";
$lang["recent_news"]    = "Recent News";

$lang["profile"]         = "Profile";
$lang["admin_panel"]     = "Admin Panel";
$lang["logout"]          = "Logout &times; ";
$lang["login_register"]  = "Login/Register";
$lang["login_fb_info"]   = "Click on the button above to sign in with your FB account";
$lang["total_comments"]  = "comment(s)";
$lang["succes_registration"]  = "You have successfully registered";

$lang["profile_changed"]  = "Profile has been successfully updated";
$lang["password_changed"] = "Password has been successfully updated";

$lang["bnet_welcome_email_subject"] = "Registration successful - Bnet account";
$lang["bnet_welcome_email"] = "You have successfully registered your Battle.net account on our website.<br />Click the link below to verify your account.<br />";
$lang["bnet_mystats"]     = "View Stats";

$lang["unconfirmed_email"]     = "Unconfirmed email";

//APPEAL
$lang["verify_appeal"]      = "Verify Banned Account";
$lang["verify_appeal_info"] = "Enter the name below to check whether the user is banned";
$lang["appeal_here"]        = "You can appeal this ban ";
$lang["here"]               = "here";
$lang["you_must_be"]        = "You must be";
$lang["logged_in"]          = "logged in";
$lang["to_appeal"]          = "to appeal";
$lang["to_use_feature"]     = "to use this feature";
$lang["appeal_ban_date"]    = "This account was banned on";
$lang["was_banned"]         = "was banned by";
$lang["appeal_for"]         = "Ban Appeal for user";
$lang["subject"]            = "Subject";
$lang["your_message"]       = "Your message here";
$lang["game_url"]           = "Game URL";
$lang["replay_url"]         = "Replay URL";

$lang["appeal_info1"] = "Your battle.net username:";
$lang["appeal_info2"] = "Here you can appeal if you are banned. To write an appeal, your account must be verified on battle net.";
$lang["appeal_info3"] = "Help?";
$lang["appeal_info4"] = "Go to";
$lang["appeal_info5"] = "your profile";
$lang["appeal_info6"] = "where you will see instructions for verifying profile.";
$lang["appeal_info7"] = "Verified accounts have battle.net icon next to the name:";

//REPORT
$lang["report_player"]      = "Report player";
$lang["report_reason"]      = "Reason for reporting player";
$lang["report_submit"]      = "Submit Report";
$lang["error_report_player"]   = "Player name does not have enough characters";
$lang["error_report_subject"]  = "Subject does not have enough characters";
$lang["error_report_reason"]   = "Text does not have enough characters";
$lang["error_no_player"]       = "No information about this player in our database";
$lang["error_already_banned"]  = "This player is already banned"; 
$lang["error_report_login"]    = "You need to login to write a report";
$lang["error_report_time"]     = "You can't quickly write ban reports. Please wait";
$lang["error_report_time2"]    = "You can't quickly write ban appeals. Please wait";

$lang["appeal_successfull"]    = "Appeal successfully added.";
$lang["report_successfull"]    = "Report successfully added.";

//Time played. Ex. 10h 23m 16s
$lang["h"]             = "h ";
$lang["m"]             = "m ";
$lang["s"]             = "s ";

//Hero stats
$lang["time_played"]             = "Time Played";
$lang["average_loading"]         = "Average loading time";
$lang["total_loading"]           = "Total loading time";
$lang["seconds"]                 = "sec.";
$lang["s"]                       = "s"; //seconds short
$lang["favorite_hero"]           = "Favorite Hero:";
$lang["most_wins"]               = "Most Wins:";
$lang["played"]                  = "Played";

//Seconds
$lang["error_sec"]             = "sec.";

$lang["game"]       = "Game";
$lang["duration"]   = "Duration";
$lang["type"]       = "Type";
$lang["date"]       = "Date";
$lang["map"]        = "Map";
$lang["creator"]    = "Creator";

$lang["hero"]    = "Hero";
$lang["player"]  = "Player";
$lang["kda"]     = "K/D/A";
$lang["cdn"]     = "C/D/N";
$lang["trc"]     = "T/R/C";
$lang["gold"]    = "Gold";
$lang["left"]    = "Left";
$lang["sent_winner"]    = "Sentinel Winner";
$lang["scou_winner"]    = "Scourge Winner";
$lang["sent_loser"]     = "Sentinel Loser";
$lang["scou_loser"]     = "Scourge Loser";
$lang["draw_game"]      = "Draw Game";

$lang["most_kills"]      = "Most Kills:";
$lang["most_assists"]    = "Most Assists:";
$lang["most_deaths"]     = "Most Deaths:";
$lang["top_ck"]          = "Top Creep Kills:";
$lang["top_cd"]          = "Top Creep Denies:";

$lang["score"]    = "Score";
$lang["games"]    = "Games";
$lang["wld"]     = "W/L/D";
$lang["wl"]     = "W/L";
$lang["tr"]     = "T/R";

$lang["sortby"]     = "Sort by:";
$lang["wins"]       = "Wins";
$lang["losses"]     = "Losses";
$lang["draw"]       = "Draw";

$lang["kills"]       = "Kills";
$lang["player_name"] = "Player name";
$lang["deaths"]      = "Deaths";
$lang["assists"]     = "Assists";
$lang["ck"]          = "Creep Kills";
$lang["cd"]          = "Creep Denies";
$lang["nk"]          = "Neutral Kills";

$lang["towers"]      = "Towers";
$lang["rax"]         = "Rax";
$lang["neutrals"]    = "Neutrals";
$lang["submit"]          = "Submit";

$lang["page"]          = "Page";
$lang["pageof"]        = "of";
$lang["total"]         = "total";
$lang["next_page"]     = "Next page";
$lang["previous_page"] = "Previous page";

$lang["fastest_game"]   = 'Fastest Game Won';
$lang["longest_game"]   = 'Longest Game Won';

$lang["game_history"]         = "Game History:";
$lang["user_game_history"]    = "User Game History";
$lang["best_player"]          = "Best Player: ";
$lang["show_hero_history"]    = "Show all games with this hero";

$lang["download_replay"]      = "Download replay";
$lang["view_gamelog"]         = "View Gamelog";

$lang["win_percent"]          = "Win %";
$lang["wl_percent"]           = "W/L%";
$lang["kd_ratio"]             = "K/D Ratio";
$lang["kd"]                   = "K/D";
$lang["kpg"]                  = "KPG";
$lang["kills_per_game"]       = "Kills per game";
$lang["dpg"]                  = "DPG";
$lang["apg"]                  = "APG";
$lang["assists_per_game"]     = "Assists per game";
$lang["ckpg"]                 = "CKPG";
$lang["creeps_per_game"]      = "Creep kills per game";
$lang["cdpg"]                 = "CDPG";
$lang["denies_per_game"]      = "Denies per game";
$lang["deaths_per_game"]      = "Deaths per game";
$lang["npg"]                  = "NPG";
$lang["neutrals_per_game"]    = "Neutrals per game";
$lang["search_results"]       = "Search results for: ";
$lang["user_not_found"]       = "User not found";
$lang["left_info"]            = "How many times a player has left the game before the end of the game";

$lang["admin"]       = "Admin";
$lang["server"]      = "Server";
$lang["voucher"]     = "Voucher";
$lang["role"]        = "Role";

$lang["banned"]     = "BANNED";
$lang["permanent_ban"]   = "Permanent";
$lang["reason"]     = "Reason";
$lang["game_name"]  = "Game Name";
$lang["bannedby"]   = "Banned by";
$lang["leaves"]     = "Leaves";
$lang["stayratio"]     = "Stay ratio";
$lang["leaver"]        = "Leaver";
$lang["streak"]        = "Streak";
$lang["longest_streak"]= "Winning Streak";
$lang["losing_streak"] = "Losing Streak";
$lang["zero_deaths"]   = "The total number of games where the player has 0 deaths";

$lang["comments"]             = "Comments";
$lang["latest_comments"]      = "Latest Comments";
$lang["add_comment"]          = "Add Comment";
$lang["add_comment_button"]   = "Add Comment";
$lang["reply"]                = "[reply]";

$lang["error_comment_not_allowed"]   = "Writing comments is not allowed for this post";
$lang["error_invalid_form"]          = "Invalid form";
$lang["error_text_char"]             = "Text does not have enough characters";

$lang["gamestate_priv"]       = "PRIV";
$lang["gamestate_pub"]        = "PUB";

//Login / Registration
$lang["login"]       = "Login";
$lang["logged_as"]   = "Logged as ";
$lang["email"]       = "E-mail";
$lang["avatar"]      = "Avatar";
$lang["location"]    = "Location";
$lang["realm"]       = "Realm";
$lang["website"]     = "Website";
$lang["gender"]      = "Gender";
$lang["male"]        = "Male";
$lang["female"]      = "Female";
$lang["password"]    = "Password";
$lang["register"]    = "Register";
$lang["username"]    = "Username";
$lang["language"]    = "Language";
$lang["confirm_password"]       = "Confirm Password";
$lang["change_password"]        = "Change Password";
$lang["change_password_info"]   = "Check this if you want to change the password";
$lang["comment_not_logged"]     = "You need to be logged in to post a comment.";
$lang["acc_activated"]          = "Account successfully activated. Now you can login.";
$lang["invalid_link"]           = "Link is not valid or expired.";

//Heroes and items
$lang["hero"]   = "Hero";
$lang["description"]     = "Description";
$lang["stats"]           = "Stats";
$lang["skills"]          = "Skills";

$lang["search"]          = "Search";
$lang["search_bans"]     = "Search bans...";
$lang["search_members"]  = "Search Members...";
$lang["search_heroes"]   = "Search heroes...";
$lang["search_items"]    = "Search items...";

//Errors
$lang["error_email"]      = "E-mail address is not valid";
$lang["error_email_banned"]      = "Email address is banned";
$lang["error_short_pw"]   = "Field Password does not have enough characters";
$lang["error_passwords"]  = "Password and confirmation password do not match";
$lang["error_inactive_acc"]   = "Account is not activated yet";
$lang["error_invalid_login"]  = "Invalid e-mail or password";
$lang["error_short_un"]   = "Field Username does not have enough characters";
$lang["error_un_taken"]   = "Username already taken";
$lang["error_username"]   = "Invalid username";
$lang["error_email_taken"]= "E-mail already taken";
$lang["error_email_send"] = "E-mail has already been sent";

//Email
$lang["email_charset"] = "UTF-8";
$lang["email_subject_activation"] = "Account Activation";
$lang["email_from"] = "no-reply@ohsystem.net";
$lang["email_from_full"] = "OpenHosting";
$lang["email_send"] = "Email message was sent successfully";

//Email text
$lang["email_activation1"] = "Hello";
$lang["email_activation2"] = "You have successfully registered to the site ";
$lang["email_activation3"] = "Click on the following link to confirm your email address and activate your account";

//GAME LOG
$lang["game_log"]            = "Game Log";
$lang["log_player"]          = "Player";
$lang["log_priv"]            = "[Priv]";
$lang["log_ally"]            = "[Allies]";
$lang["log_first_blood"]     = "for first blood";
$lang["log_suicide"]         = "has killed himself!";
$lang["log_denied_teammate"] = "denied his teammate";
$lang["log_level"]           = "level";
$lang["log_tower"]           = "tower";
$lang["log_barracks"]        = "barracks";

$lang["404_error"]           = "Oops, page not found";

//VOTES
$lang["vote_title"]          = "Vote for favorite";
$lang["votes_won"]           = "Votes Won";
$lang["votes_lost"]          = "Votes Lost";
$lang["votes_total"]         = "Total Votes";
$lang["votes_best"]          = "Best";
$lang["vote_vs"]             = "VS";
$lang["vote_sort"]           = "Submit";
$lang["vote_results"]        = "Vote Results";
$lang["vote_back"]           = "Back to Vote";

$lang["vote_won"]           = "Won:";
$lang["vote_lost"]          = "Lost:";
$lang["vote_again"]         = "Vote again?";

$lang["vote_error1"]        = "Please click on the hero to vote.";
$lang["vote_display"]       = "Display results";

$lang["read_more"]          = "...[more]";

$lang["upload_image"]      = "Upload image";
$lang["remove_avatar"]     = "Remove avatar";

//Gamelist patch
$lang["current_games"]      = "Current Games";
$lang["refresh"]            = "Refresh";
$lang["slots"]              = "Slots";
$lang["empty"]              = "Empty";
$lang["ms"]                 = "ms";
$lang["players"]            = "Players";

//Members
$lang["joined"]         = "Joined";
$lang["user_info"]      = "Info";

$lang["choose"]         = "Choose";
$lang["all_guides"]     = "---All guides---";

//Compare players
$lang["compare_back"]         = "&laquo; Back to Top players";
$lang["compare_list"]         = "List";
$lang["compare_list_empty"]   = "List is empty";
$lang["compare_compare"]      = "Compare";
$lang["compare_add"]          = "Add to compare list";
$lang["compare_clear"]        = "Clear list";
$lang["compare_remove_player"]   = "Remove player from list?";
$lang["compare_players"]      = "Player Comparison";
$lang["compare_empty_info"]   = "Your list of players to compare is empty";
$lang["overall"]              = "Overall";
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

$lang["gl_login"] = "Please <a href='".$website."?login'>login</a> to view all the action in the game";

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
$lang["acount_is_protected"] = "Your account is protected on Battle.net";
$lang["acount_is_not_protected"] = "Your account is not protected on Battle.net";
$lang["acount_protected_step"] = "One more step to protect your account";
$lang["set_password"]        = "Set Password";
$lang["protect_info1"]       = "You can protect your account with a password.";
$lang["protect_info2"]       = "<b>!pw</b> <i>[password]</i> - verify your account in game/lobby";
$lang["protect_info3"]       = "<b>!pw</b> <i>[password] clear</i> - remove protection";
$lang["protect_info4"]       = "More info";
$lang["protect_info5"]       = "By setting a password, your account can not be abused by someone else on Battle.net (use the same name as yours)";
$lang["protect_info6"]       = "Usage";
$lang["protect_info7"]       = "If you write the password in the field above <b>mypassword</b>, you can use the command";
$lang["protect_info8"]       = "<b>!pw mypassword</b> to protect your account";
$lang["protect_info9"]       = "<b>!pw mypassword clear</b> - remove protection or";
$lang["protect_info10"]      = "<b>!pw mypassword 0</b> - remove protection";
$lang["protect_info11"]      = "<b>Note:</b> If the password is set, and you <b>did not</b> use the command <b>!pw</b> you will be kicked within 10 seconds";
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

$lang["hide_stats"]          = "Do not show my stats";
$lang["show_stats"]          = "Show my stats";
$lang["hide_stats_info"]     = "If you hide the statistics, no one will be able to see your statistics (except for individual games).<div>You must confirm your Battle net account to change this option.</div>";
$lang["hide_stats_message"]  = "The user does not want to show his statistics";
$lang["last_seen"]           = "Last seen";
$lang["displlay_stats_for"]  = "Displaying statistics for";
$lang["select_country"]      = "Select country";

$lang["most_recent_stats"]  = "Show most recent statistics";

$lang["game_log_lobby"]  = "Lobby Log";
$lang["game_log_game"]   = "Game Log";
?>