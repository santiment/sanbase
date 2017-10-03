BEGIN;

CREATE TABLE tracked_btc
(
  address text NOT NULL,
  PRIMARY KEY (address)
);

CREATE TABLE project_btc_address
(
  project_id integer REFERENCES project (id),
  address text NOT NULL UNIQUE,
  PRIMARY KEY (address)
);

CREATE TABLE latest_btc_wallet_data
(
  address text UNIQUE,
  satoshi_balance bigint NOT NULL,
  update_time timestamp NOT NULL,
  PRIMARY KEY (address)
);

COMMIT;
