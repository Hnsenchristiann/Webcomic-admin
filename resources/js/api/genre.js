import request from '@/utils/request';

export function fetchGenres(query) {
  return request({
    url: '/genres',
    method: 'get',
    params: query,
  });
}

export function fetchGenre(id, query) {
  return request({
    url: '/genres/' + id,
    method: 'get',
    params: query,
  });
}
