/*
 * Author:  Grief-Code
 * Project: OHSystem ghost_v2
 * Support: admin@gcoding.net
 * Date: 16.02.2014
 * Version: 0.9
 *
 * File: logger.h
 * Desc: A file to handle log entries over the whole project
 * - generate game logs
 * - generate lobby logs
 * - generate admin logs
 * - warper for statspage class
 * - warper for ohconnect class
 */

#ifndef LOGGER_H
#define LOGGER_H

#include <vector>
#include <string>
#include <memory>
using namespace std;

class Log {
private:
	vector<string> Logs;
public:
	inline void addEvent(string msg) {
		Logs.push_back(msg);
	}
	inline vector<string> getLog() {
		return Logs;
	}
	virtual ~Log () {
		Logs.clear();
	}
};

class Logger {
protected:
public:
        Logger( );
        virtual ~Logger( );
};
#endif

