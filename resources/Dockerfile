FROM debian:8

WORKDIR /var/www/html

COPY ./oracle-instantclient*.rpm /tmp/

ARG IMAGE_DESCRIPTION
LABEL Description=${IMAGE_DESCRIPTION}

# Update the system and install required packages
RUN ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && \
dpkg-reconfigure -f noninteractive tzdata && \
echo "deb-src http://deb.debian.org/debian jessie main" >> /etc/apt/sources.list && \
apt-get update && apt-get upgrade -y && \
apt-get install -y curl libapache2-mod-php5 && \
cd /tmp/ && apt-get install -y alien dpkg-dev build-essential php5-dev php5-mysql php5-pgsql php5-curl php5-xmlrpc php5-xdebug libaio1 wget vim && apt-get source -y php5 && \
cd /tmp/ && alien -d /tmp/oracle-instantclient-basic-21.1.0.0.0-1.x86_64.rpm && alien -d oracle-instantclient-devel-21.1.0.0.0-1.x86_64.rpm && \
dpkg -i /tmp/oracle-instantclient-basic_21.1.0.0.0-2_amd64.deb && dpkg -i /tmp/oracle-instantclient-devel_21.1.0.0.0-2_amd64.deb && \
export ORACLE_HOME="/usr/lib/oracle/21/client64"; export LD_LIBRARY_PATH="${ORACLE_HOME}/lib"; ldconfig; ln -s /usr/include/php5/ /usr/include/php; cd /tmp/php5-5.6.33+dfsg/ext/pdo_oci; phpize; ./configure

COPY ./Makefile /tmp/php5-5.6.33+dfsg/ext/pdo_oci/Makefile

RUN cd /tmp/php5-5.6.33+dfsg/ext/pdo_oci; make && make install

COPY ./pdo_oci.ini /etc/php5/mods-available/pdo_oci.ini
COPY ./xdebug.ini /etc/php5/mods-available/xdebug.ini

RUN cd /tmp && wget https://pecl.php.net/get/oci8-2.0.7.tgz && \
tar xzf oci8-2.0.7.tgz && \
cd oci8-2.0.7 && phpize; ./configure && make && make install

CMD chmod 644 /usr/lib/php5/20131226/oci8.so

RUN echo '; priority=10' > /etc/php5/mods-available/oci8.ini && \
    echo 'extension=oci8.so' >> /etc/php5/mods-available/oci8.ini

CMD chmod 644 /etc/php5/mods-available/oci8.ini

RUN rm -Rf /tmp/php5* /tmp/oracle-instantclient* && apt-get autoremove -y --purge alien dpkg-dev build-essential php5-dev && apt-get clean

ENTRYPOINT ["/tmp/entrypoint.sh"]



