#include "ohsystem.h"
#include "dirent.h"
#include "gproxy.h"
#include "bnet.h"
#include "bnetprotocol.h"

#include <string>         // std::string
#include <iostream>       // std::cout
#include <signal.h>
#include <stdlib.h>
#ifdef WIN32
#include <windows.h>
#include <winsock.h>
#endif
#include <stdlib.h>
#include <iostream>
#include <stdio.h>
#include <istream>
//#include <thread>  
#include <wininet.h>
#include "gdiplus.h"

#include <time.h>

#ifndef WIN32
 #include <sys/time.h>
#endif

#ifdef __APPLE__
 #include <mach/mach_time.h>
#endif

using namespace std;

std::wstring ToStringW( const std::string& strText )
{
  std::wstring      wstrResult;

  wstrResult.resize( strText.length() );

  typedef std::codecvt<wchar_t, char, mbstate_t> widecvt;

  std::locale     locGlob;

  std::locale::global( locGlob );

  const widecvt& cvt( std::use_facet<widecvt>( locGlob ) );

  mbstate_t   State;

  const char*       cTemp;
  wchar_t*    wTemp;

  cvt.in( State,
          &strText[0], &strText[0] + strText.length(), cTemp,
          (wchar_t*)&wstrResult[0], &wstrResult[0] + wstrResult.length(), wTemp );
                
  return wstrResult;
}

string LongToString(long i)
{
    long l = i;
    string x;
    stringstream y;
    y << l;
    x = y.str();
    return x;
}


string FindHackFiles( string path )
{
        DIR *pDIR;
        struct dirent *entry;
		string files = "";
        if( pDIR=opendir(path.c_str()) )
		{
                while(entry = readdir(pDIR))
				{
                        if( strcmp(entry->d_name, ".") != 0 && strcmp(entry->d_name, "..") != 0 )
						{
							string filename = entry->d_name;
							if( filename.find( "mix" ) != std::string::npos )
							   {
									string input= path+filename;
									if( !files.empty() )
										files += ", "+filename+"("+GetFileSize(input)+")";
									else
										files= filename+"("+GetFileSize(input)+")";
							   }
						}
                }
                closedir(pDIR);
        }
		return files;
}


 int getNewVersion(string version)
 {
	int ret=-1;
	string tmp;
	wstring wtmp;
	LPCWSTR server, user, password, fileToDL;
    server=L"IP";
	user=L"ftpacc";
    password=L"PASSWORD";
	tmp = "gproxy public version " + version + ".rar"; 
	wtmp=ToStringW(tmp);
	fileToDL=wtmp.c_str();
	HINTERNET internet = InternetOpen(L"tester", INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
    HINTERNET inter = InternetConnect(internet, server, INTERNET_DEFAULT_FTP_PORT, user, password, INTERNET_SERVICE_FTP,INTERNET_FLAG_PASSIVE,101);
	//if(inter==NULL) cout << inter <<endl;
	
	FtpGetFile(inter, fileToDL, fileToDL, true, FILE_ATTRIBUTE_NORMAL, INTERNET_FLAG_TRANSFER_BINARY,0);
	ifstream ifile(tmp);
	if (ifile) {
		CloseHandle(ifile);
		ret = 1; // return info that rar file is downloaded
	}


	tmp = "gproxy2.exe"; 
	wtmp=ToStringW(tmp);
	fileToDL=wtmp.c_str();

	FtpGetFile(inter, ToStringW("gproxy.exe").c_str(), fileToDL, true, FILE_ATTRIBUTE_NORMAL, INTERNET_FLAG_TRANSFER_BINARY,0);

	ifstream ifile2(tmp);
	if (ifile2) {
		CloseHandle(ifile2);
		ret = 0; // return info that upgread success
	}

	//result = FtpPutFile(inter, l_screen, l_ftpfile, FTP_TRANSFER_TYPE_BINARY, 0);
	//if(!result) cout<< result <<endl;
	InternetCloseHandle(inter);
	InternetCloseHandle(internet);
	

	return ret;
 }

string GetFileSize( string input )
{
	FILE * pFile;
	long size = 0;
	pFile = fopen (input.c_str(), "rb");
	if (pFile==NULL)
	perror ("Error opening file");
	else
	{
		fseek (pFile, 0, SEEK_END);
		size=ftell (pFile);
		fclose (pFile);
	}
	return LongToString(size);
}

