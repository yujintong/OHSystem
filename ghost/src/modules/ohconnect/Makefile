SHELL = /bin/sh
SYSTEM = $(shell uname)
C++ = g++
CC = gcc
DFLAGS = 
OFLAGS = -O3
LFLAGS = -L.
CFLAGS =

ifeq ($(SYSTEM),Darwin)
DFLAGS += -D__APPLE__
OFLAGS += -flat_namespace
else
LFLAGS += -lrt
endif

ifeq ($(SYSTEM),FreeBSD)
DFLAGS += -D__FREEBSD__
endif

ifeq ($(SYSTEM),SunOS)
DFLAGS += -D__SOLARIS__
LFLAGS += -lresolv -lsocket -lnsl
endif

CFLAGS += $(OFLAGS) $(DFLAGS) -I. -I../../

OBJS = ohconnect.o
COBJS =

all: $(OBJS) $(COBJS) $(PROGS)

./ghost++: $(OBJS) $(COBJS)
	$(C++) -o ./ghost++ $(OBJS) $(COBJS) $(LFLAGS)

clean:
	rm -f $(OBJS) $(COBJS) $(PROGS)

$(OBJS): %.o: %.cpp
	$(C++) -o $@ $(CFLAGS) -c $<
