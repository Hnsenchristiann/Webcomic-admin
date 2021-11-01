import request from '@/utils/request';

export function fetchComics(query) {
  return request({
    url: '/comics',
    method: 'get',
    params: query,
  });
}

export function fetchComic(id, query) {
  return request({
    url: '/comics/' + id,
    method: 'get',
    params: query,
  });
}

export function fetchPv(id) {
  return request({
    url: '/comics/' + id + '/pageviews',
    method: 'get',
  });
}

export function createComic(data) {
  return request({
    url: '/comic',
    method: 'post',
    data,
  });
}

export function updateComic(id, data) {
  return request({
    url: '/comic/' + id,
    method: 'patch',
    data,
  });
}

export function fetchTransactions(id) {
  return request({
    url: '/comics/' + id + '/transactions',
    method: 'get',
  });
}
