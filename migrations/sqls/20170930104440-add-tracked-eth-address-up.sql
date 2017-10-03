BEGIN;

CREATE TABLE tracked_eth
(
  address text NOT NULL,
  PRIMARY KEY (address)
);

CREATE TABLE project
(
  id serial PRIMARY KEY,
  name text NOT NULL,
  ticker text NOT NULL,
  UNIQUE (name)
);

CREATE TABLE project_eth_address
(
  project_id integer REFERENCES project (id),
  address text NOT NULL UNIQUE,
  PRIMARY KEY (address)
);

CREATE TABLE latest_eth_wallet_data
(
  address text UNIQUE,
  balance real NOT NULL,
  update_time timestamp NOT NULL,
  PRIMARY KEY (address)
);

END;
